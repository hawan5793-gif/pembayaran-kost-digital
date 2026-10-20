with open('app_source_dump.txt', 'r', encoding='utf-8') as f:
    text = f.read()

import re
idx = text.find('viewBox:`0 0 24 24`')
if idx != -1:
    print(text[max(0, idx-1000):min(len(text), idx+2500)])
else:
    idx2 = text.find('viewBox')
    print(text[max(0, idx2-200):min(len(text), idx2+1500)])

