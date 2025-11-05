-- Fix role assignment for walid.babi.dubai@gmail.com
-- This script assigns the purchasing_crm_assistant role to the user

-- First, let's check if the user exists and their current role
SELECT 
    u.id,
    u.name,
    u.email,
    u.role_id,
    r.name as role_name,
    r.display_name as role_display_name
FROM users u
LEFT JOIN roles r ON u.role_id = r.id
WHERE u.email = 'walid.babi.dubai@gmail.com';

-- Check if the purchasing_crm_assistant role exists
SELECT 
    id,
    name,
    display_name,
    description,
    is_active
FROM roles 
WHERE name = 'purchasing_crm_assistant';

-- If the role doesn't exist, create it (run this only if needed)
-- INSERT INTO roles (name, display_name, description, is_active, created_at, updated_at)
-- VALUES ('purchasing_crm_assistant', 'Purchasing & CRM Assistant', 'Assists with purchasing processes and manages own CRM leads', 1, NOW(), NOW());

-- Find the role ID (replace with actual ID from the SELECT above)
-- Let's assume the role ID is 7 (adjust based on actual result)
SET @role_id = (SELECT id FROM roles WHERE name = 'purchasing_crm_assistant' LIMIT 1);

-- Update the user's role
UPDATE users 
SET role_id = @role_id,
    updated_at = NOW()
WHERE email = 'walid.babi.dubai@gmail.com';

-- Verify the update
SELECT 
    u.id,
    u.name,
    u.email,
    u.role_id,
    r.name as role_name,
    r.display_name as role_display_name
FROM users u
LEFT JOIN roles r ON u.role_id = r.id
WHERE u.email = 'walid.babi.dubai@gmail.com';

-- Show the role's permissions
SELECT 
    p.name as permission_name,
    p.display_name as permission_display_name
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
JOIN roles r ON rp.role_id = r.id
WHERE r.name = 'purchasing_crm_assistant'
ORDER BY p.name;