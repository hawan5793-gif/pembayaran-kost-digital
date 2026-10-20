with open('C:/xampp/htdocs/project_laravel/kostsaya/public/figma-bundle.js', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('function w(')
if idx != -1:
    print(text[idx-500:idx+2500])
else:
    idx2 = text.find('bottom-nav')
    print(text[idx2-500:idx2+1500])

