with open('app_source_dump.txt', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('bell:(0,x.jsxDEV)')
if idx != -1:
    print(text[max(0, idx-1000):min(len(text), idx+1500)])

