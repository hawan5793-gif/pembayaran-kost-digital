with open('C:/xampp/htdocs/project_laravel/kostsaya/public/figma-bundle.js', 'r', encoding='utf-8') as f:
    text = f.read()

idx = text.find('S=`/workspaces/default/.publishing/src/App.tsx`')
print("Index:", idx)
sub = text[idx:]

with open('App_original.js', 'w', encoding='utf-8') as f:
    f.write(sub)

print("Saved App_original.js, length:", len(sub))
