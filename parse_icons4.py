with open('app_source_dump.txt', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('calendar:(0,x.jsxDEV)')
if idx != -1:
    print(text[idx-200:idx+1500])

