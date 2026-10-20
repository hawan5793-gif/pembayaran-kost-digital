import re

with open('app_source_dump.txt', 'r', encoding='utf-8') as f:
    text = f.read()

# Cari fungsi-fungsi utama
funcs = re.findall(r'function\s+([a-zA-Z0-9_]+)\s*\(([^)]*)\)\s*\{', text)
for name, params in funcs:
    print(f"{name}({params})")

