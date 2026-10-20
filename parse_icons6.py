with open('app_source_dump.txt', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('M4 20V10M10 20V4M16 20v-7M22 20H2')
if idx != -1:
    print(text[max(0, idx-1000):idx])

