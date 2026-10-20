with open('App_original.js', 'r', encoding='utf-8') as f:
    code = f.read()

# Let's inspect T, ae, oe, E
for name in ['re', 'w', 'T', 'ae', 'oe', 'E']:
    pos = code.find(f'function {name}(')
    print(f"==================== FUNCTION {name} ====================")
    print(code[pos:pos+2500])

