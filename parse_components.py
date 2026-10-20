with open('app_source_dump.txt', 'r', encoding='utf-8') as f:
    text = f.read()

import re

for fn in ['ie', 'T', 'ae', 'oe', 'E', 'se']:
    m = re.search(r'function\s+' + fn + r'\s*\([^)]*\)\s*\{', text)
    if m:
        start = m.start()
        # cari next function
        next_m = re.search(r'function\s+[a-zA-Z0-9_]+\s*\([^)]*\)\s*\{', text[start+20:])
        end = start + 20 + next_m.start() if next_m else len(text)
        chunk = text[start:min(start+1500, end)]
        print(f"=== FUNCTION {fn} ===")
        print(chunk[:800])
        print("\n")
