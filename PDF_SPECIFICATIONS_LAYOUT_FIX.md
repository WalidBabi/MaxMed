# PDF Specifications Layout Fix

## Summary of Changes

### 1. ✅ Dynamic Column Widths (COMPLETED)
The table now automatically adjusts column widths when Notes are present:
- **With Notes:** Description 30%, Specifications 35% (wider for Notes content)
- **Without Notes:** Description 40%, Specifications 20%

### 2. 🔧 Specifications Order (NEEDS MANUAL EDIT)

**File:** `resources/views/admin/quotes/pdf.blade.php`  
**Lines:** 671-717

**Current Order:**
1. Specifications (Notes, etc.)
2. Size
3. Model

**New Order (Required):**
1. **Model** (at the top)
2. **Size**
3. **Specifications** (Notes, etc.) with special styling

### 3. Manual Edit Instructions

Replace lines 671-717 in `resources/views/admin/quotes/pdf.blade.php` with:

```blade
                        <td class="specifications">
                            {{-- Show Model first if present --}}
                            @if(!empty($item->model))
                                <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.4; margin-bottom: 5px;">
                                    <span style="font-weight: 600; color: var(--text-primary);">Model:</span> {{ $item->model }}
                                </div>
                            @endif
                            
                            {{-- Then show Size if present --}}
                            @if($item->size && !empty(trim($item->size)))
                                <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.4; margin-bottom: 5px;">
                                    <span style="font-weight: 600; color: var(--text-primary);">Size:</span> {{ $item->size }}
                                </div>
                            @endif
                            
                            {{-- Then show other specifications --}}
                            @if($item->specifications && !empty(trim($item->specifications)))
                                @php
                                    $selectedSpecs = [];
                                    try {
                                        if (is_string($item->specifications) && (str_starts_with($item->specifications, '[') && str_ends_with($item->specifications, ']'))) {
                                            $selectedSpecs = json_decode($item->specifications, true);
                                        } else {
                                            $selectedSpecs = explode(',', $item->specifications);
                                            $selectedSpecs = array_map('trim', $selectedSpecs);
                                        }
                                    } catch (Exception $e) {
                                        $selectedSpecs = [$item->specifications];
                                    }
                                @endphp
                                
                                @if(count($selectedSpecs) > 0)
                                    <div style="font-size: 8.5px; color: var(--text-secondary); line-height: 1.4;">
                                        @foreach($selectedSpecs as $spec)
                                            @if(is_array($spec) && isset($spec['type']) && $spec['type'] === 'image')
                                                {{-- Display specification image --}}
                                                <div style="margin-bottom: 4px;">
                                                    <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 2px;">{{ $spec['value'] ?? 'Specification Image' }}</div>
                                                    <img src="{{ $spec['url'] }}" alt="Specification" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; border-radius: 4px;">
                                                </div>
                                            @else
                                                @php 
                                                    $specText = is_string($spec) ? $spec : (is_array($spec) ? ($spec['value'] ?? '') : json_encode($spec));
                                                    // Check if it's Notes
                                                    $isNotes = str_contains($specText, 'Notes:') || str_contains($specText, 'Package Includes');
                                                @endphp
                                                <div style="margin-bottom: 3px; {{ $isNotes ? 'padding: 4px; background: #f9fafb; border-left: 2px solid #6366f1; border-radius: 3px;' : '' }}">
                                                    {!! nl2br(e($specText)) !!}
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </td>
```

### 4. Visual Improvements

**Notes Styling:**
- Light grey background (#f9fafb)
- Indigo left border (2px solid #6366f1)
- Padding for better readability
- Smaller font size (8.5px) to fit more content
- Better line height (1.4) for readability

**Model & Size:**
- Bold labels
- Inline format: "Model: DPCD100T"
- Appears at the top of specifications column

### 5. Testing

After making the changes:
1. Go to Admin → Quotes → View any quote with Notes
2. Click "Download PDF"
3. Verify:
   - ✅ Model appears at the top
   - ✅ Size appears second
   - ✅ Notes appear last with grey background and indigo border
   - ✅ Specifications column is wider (35%) when Notes present
   - ✅ Text is readable and not cut off

### 6. Apply Same Fix to Invoice PDF

**File:** `resources/views/admin/invoices/pdf.blade.php`  
Apply the same changes to the invoice PDF template.

---

## Summary

**Completed:**
- ✅ Dynamic column width adjustment
- ✅ Table header width optimization

**Requires Manual Edit:**
- 🔧 Replace lines 671-717 in quotes PDF
- 🔧 Apply same to invoices PDF

**Result:**
- Model shown first (most important info)
- Notes displayed with special styling for easy reading
- Wider specifications column when Notes are present
- Better overall PDF presentation

