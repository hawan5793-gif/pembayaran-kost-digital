with open('C:/xampp/htdocs/project_laravel/kostsaya/public/figma-bundle.js', 'r', encoding='utf-8') as f:
    text = f.read()

import re
matches = re.findall(r'view===`([^`]+)`', text)
print("Template string view matches:", set(matches))

# Cari semua komponen halaman
comps = re.findall(r'function\s+([A-Z][a-zA-Z0-9_]*)\s*\([^)]*\)\s*\{', text)
print("React components:", [c for c in comps if any(k in c.lower() for k in ['home', 'tenant', 'history', 'report', 'record', 'notif', 'pay', 'page', 'app'])])

