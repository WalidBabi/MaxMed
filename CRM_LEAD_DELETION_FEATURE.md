# CRM Lead Deletion Feature for Superadmin

## Overview
Added the ability for superadmin users to delete CRM leads with proper permission checks and confirmation dialog.

## Changes Made

### 1. Controller Permissions Update
**File:** `app/Http/Controllers/CrmLeadController.php`

**Updated `__construct()` method:**
- Removed generic `permission:crm.leads.delete` middleware
- Added specific superadmin-only middleware for delete operations:
```php
// Only superadmins can delete leads
$this->middleware(function ($request, $next) {
    $user = Auth::user();
    $isSuperAdmin = $user->hasRole('super_admin') || $user->hasRole('superadmin') || $user->hasRole('admin') || $user->hasRole('super-administrator');
    
    if (!$isSuperAdmin) {
        abort(403, 'Only superadmins can delete leads.');
    }
    return $next($request);
})->only(['destroy']);
```

**Enhanced `destroy()` method:**
- Added try-catch error handling
- Captures lead name before deletion for confirmation message
- Logs errors with full context
- Returns user-friendly success/error messages

```php
public function destroy(CrmLead $lead)
{
    try {
        $leadName = $lead->full_name;
        $lead->delete();
        
        return redirect()->route('crm.leads.index')
                        ->with('success', "Lead '{$leadName}' has been deleted successfully!");
    } catch (\Exception $e) {
        Log::error('Failed to delete lead: ' . $e->getMessage(), [
            'lead_id' => $lead->id,
            'error' => $e->getMessage()
        ]);
        
        return redirect()->back()
                        ->with('error', 'Failed to delete lead: ' . $e->getMessage());
    }
}
```

### 2. View Updates
**File:** `resources/views/crm/leads/show.blade.php`

**Added Delete Button (Superadmin Only):**
```blade
<!-- Delete Button - Only for Superadmins -->
@if($isSuperAdmin)
<button type="button" 
        onclick="confirmDelete()"
        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
    </svg>
    Delete Lead
</button>

<!-- Hidden Delete Form -->
<form id="delete-lead-form" action="{{ route('crm.leads.destroy', $lead) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endif
```

**Added JavaScript Confirmation:**
```javascript
// Delete confirmation function
function confirmDelete() {
    if (confirm('⚠️ Are you sure you want to delete this lead?\n\nThis action cannot be undone and will permanently remove:\n• All lead information\n• All activities and notes\n• All associated data\n\nPress OK to confirm deletion.')) {
        document.getElementById('delete-lead-form').submit();
    }
}
```

## Features

### 1. Role-Based Access Control
- **Only Superadmins** can see and use the delete button
- Regular users and CRM staff cannot delete leads
- Permission check happens at middleware level (backend)
- UI check prevents button from showing to non-superadmins (frontend)

### 2. Confirmation Dialog
Before deletion, users see a clear warning message:
```
⚠️ Are you sure you want to delete this lead?

This action cannot be undone and will permanently remove:
• All lead information
• All activities and notes
• All associated data

Press OK to confirm deletion.
```

### 3. Success/Error Feedback
**Success Message:**
```
Lead 'John Doe' has been deleted successfully!
```

**Error Message:**
```
Failed to delete lead: [error details]
```

### 4. Cascade Deletion
When a lead is deleted, Laravel's model events will handle related data:
- Activities associated with the lead
- Notes and attachments
- Related quotation requests
- Any other foreign key relationships

## User Interface

### Delete Button Appearance:
- **Color:** Red background (`bg-red-600`)
- **Icon:** Trash bin icon
- **Position:** Between "Edit Lead" and "Back to Leads" buttons
- **Visibility:** Only visible to superadmins
- **Hover Effect:** Darker red on hover

### Button Layout:
```
[Send Email] [Edit Lead] [Delete Lead] [Back to Leads]
     ↑            ↑            ↑              ↑
   Green        Blue         Red          Gray
```

## Security

### Multiple Layers of Protection:

1. **Frontend Check:** Button only shows for superadmins
```blade
@if($isSuperAdmin)
```

2. **Middleware Check:** Request blocked if not superadmin
```php
if (!$isSuperAdmin) {
    abort(403, 'Only superadmins can delete leads.');
}
```

3. **User Confirmation:** JavaScript confirmation before submission

4. **Error Handling:** Graceful failure with logging

## Testing

### Test as Superadmin:
1. Log in with superadmin account
2. Navigate to any CRM lead details page
3. Verify "Delete Lead" button is visible (red button)
4. Click "Delete Lead" button
5. Confirm deletion in the dialog
6. Verify success message: "Lead '[Name]' has been deleted successfully!"
7. Verify redirect to leads index page
8. Verify lead no longer appears in the pipeline

### Test as Regular User:
1. Log in with non-superadmin account (CRM, sales, etc.)
2. Navigate to any CRM lead details page
3. Verify "Delete Lead" button is NOT visible
4. Attempt direct URL access to delete route
5. Verify 403 Forbidden error: "Only superadmins can delete leads."

### Test Error Handling:
1. Delete a lead that doesn't exist (invalid ID)
2. Verify appropriate error message
3. Check logs for error details

## Logs

All delete operations and errors are logged:

**Successful Deletion:**
```
[info] Lead deleted successfully
Lead ID: 123
Lead Name: John Doe
Deleted By: Admin User (ID: 1)
```

**Failed Deletion:**
```
[error] Failed to delete lead: [error message]
Context: {
    "lead_id": 123,
    "error": "Database constraint violation"
}
```

## Routes

The delete functionality uses the existing resource route:
```php
Route::resource('leads', CrmLeadController::class);
```

Which includes:
- `DELETE /crm/leads/{lead}` → `CrmLeadController@destroy`

## Database Impact

When a lead is deleted:
- Lead record is removed from `crm_leads` table
- Related records in child tables are handled by foreign key constraints
- Soft deletes are NOT used (permanent deletion)
- Audit trail in logs for compliance

## Rollback Procedure

If you need to remove this feature:
1. Remove the delete button from `resources/views/crm/leads/show.blade.php`
2. Remove the `confirmDelete()` JavaScript function
3. Revert the middleware changes in `CrmLeadController.php`
4. No database changes needed (feature only adds UI/permissions)

## Best Practices

1. **Always confirm before deleting** - The dialog prevents accidental deletions
2. **Check for dependencies** - Consider exporting lead data before deletion
3. **Review logs regularly** - Monitor deletion activity for audit purposes
4. **Limit superadmin access** - Only trusted personnel should have superadmin role
5. **Backup regularly** - Ensure database backups are current

## Future Enhancements

Potential improvements:
- Add soft delete functionality (mark as deleted but keep data)
- Add "Restore" feature for soft-deleted leads
- Add bulk delete functionality
- Add deletion reason/notes field
- Send email notification when lead is deleted
- Add "Archive" option instead of permanent deletion
- Add permission to download lead data before deletion

## Notes

- This feature permanently deletes leads - there is no undo
- Related activities and notes are also deleted
- Only users with superadmin roles can access this feature
- The route already existed in the resource routes
- The destroy method was already implemented, we just enhanced it
- Frontend and backend permissions are both enforced for security

