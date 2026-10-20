with open('C:/xampp/htdocs/project_laravel/kostsaya/public/figma-bundle.js', 'r', encoding='utf-8') as f:
    text = f.read()

# Let's search from the end or find all occurrences of bottom-nav
pos = 0
while True:
    idx = text.find('bottom-nav', pos)
    if idx == -1:
        break
    print("Found at", idx)
    print(text[max(0, idx-1000):min(len(text), idx+2000)])
    pos = idx + 10

