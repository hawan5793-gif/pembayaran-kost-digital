with open('app_source_dump.txt', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('lineNumber:44')
if idx != -1:
    print(text[max(0, idx-1500):idx+500])

