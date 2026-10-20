with open('app_source_dump.txt', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('function C(')
if idx != -1:
    print(text[idx:idx+3500])
else:
    # cari kata bell atau users
    idx2 = text.find('name:`bell`')
    print(text[idx2-500:idx2+1000])

