with open('App_original.js', 'r', encoding='utf-8') as f:
    code = f.read()

pos_ie = code.find('function ie(')
pos_T = code.find('function T(')
print(code[pos_ie:pos_T])

