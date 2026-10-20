with open('App_original.js', 'r', encoding='utf-8') as f:
    code = f.read()

import re

# Split by function
func_names = ['C', 're', 'w', 'ie', 'T', 'ae', 'oe', 'E', 'se']
positions = []
for name in func_names:
    m = re.search(r'function\s+' + name + r'\s*\(', code)
    if m:
        positions.append((m.start(), name))

positions.sort()

for i, (pos, name) in enumerate(positions):
    end = positions[i+1][0] if i+1 < len(positions) else len(code)
    fn_code = code[pos:end]
    print(f"============================== {name} ==============================")
    # Print clean version (remove JSX boilerplate if possible, or print first 1500 chars)
    # Just print the first 1200 chars
    print(fn_code[:1200])
    print("\n")
