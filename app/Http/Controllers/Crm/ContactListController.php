<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\ContactList;
use App\Models\MarketingContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ContactListController extends Controller
{
    public function index()
    {
        $contactLists = ContactList::with('creator')
            ->withCount('contacts')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('crm.marketing.contact-lists.index', compact('contactLists'));
    }

    public function create()
    {
        $marketingContacts = MarketingContact::active()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
            
        return view('crm.marketing.contact-lists.create', compact('marketingContacts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:contact_lists,name',
            'description' => 'nullable|string',
            'type' => 'required|in:static,dynamic',
            'criteria' => 'nullable|array',
            'contacts' => 'nullable|array',
            'contacts.*' => 'exists:marketing_contacts,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $contactList = ContactList::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'criteria' => $request->criteria,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        // If it's a dynamic list, refresh the contacts based on criteria
        if ($contactList->isDynamic()) {
            $contactList->refreshDynamicContacts();
        }

        // If it's a static list and contacts are selected, add them
        if ($contactList->isStatic() && $request->filled('contacts')) {
            $contactList->contacts()->attach($request->contacts, [
                'added_at' => now()
            ]);
        }

        return redirect()->route('crm.marketing.contact-lists.index')
                        ->with('success', 'Contact list created successfully.');
    }

    public function show(ContactList $contactList)
    {
        $contactList->load('creator');
        
        if ($contactList->isDynamic()) {
            $contacts = $contactList->getDynamicContacts()->paginate(25);
        } else {
            $contacts = $contactList->contacts()->paginate(25);
        }

        return view('crm.marketing.contact-lists.show', compact('contactList', 'contacts'));
    }

    public function edit(ContactList $contactList)
    {
        $marketingContacts = MarketingContact::active()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
            
        return view('crm.marketing.contact-lists.edit', compact('contactList', 'marketingContacts'));
    }

    public function update(Request $request, ContactList $contactList)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:contact_lists,name,' . $contactList->id,
            'description' => 'nullable|string',
            'type' => 'required|in:static,dynamic',
            'criteria' => 'nullable|array',
            'is_active' => 'boolean',
            'contacts' => 'nullable|array',
            'contacts.*' => 'exists:marketing_contacts,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $contactList->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'criteria' => $request->criteria,
            'is_active' => $request->boolean('is_active'),
        ]);

        // If it's a dynamic list, refresh the contacts based on new criteria
        if ($contactList->isDynamic()) {
            $contactList->refreshDynamicContacts();
        }

        // If it's a static list, sync the contacts
        if ($contactList->isStatic()) {
            if ($request->has('contacts')) {
                $contactList->contacts()->sync($request->contacts ?: []);
            }
        }

        return redirect()->route('crm.marketing.contact-lists.show', $contactList)
                        ->with('success', 'Contact list updated successfully.');
    }

    public function destroy(ContactList $contactList)
    {
        $contactList->delete();

        return redirect()->route('crm.marketing.contact-lists.index')
                        ->with('success', 'Contact list deleted successfully.');
    }

    public function refresh(ContactList $contactList)
    {
        if ($contactList->isDynamic()) {
            $contactList->refreshDynamicContacts();
            return redirect()->back()
                           ->with('success', 'Dynamic contact list refreshed successfully.');
        }

        return redirect()->back()
                       ->with('error', 'Only dynamic contact lists can be refreshed.');
    }
} 