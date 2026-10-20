with open('app_source_dump.txt', 'r', encoding='utf-8') as f:
    text = f.read()

import re

# Cetak data array awal (ee, te, dll)
idx = text.find('function re(')
print("=== INITIAL DATA / CONSTANTS ===")
print(text[:idx])

