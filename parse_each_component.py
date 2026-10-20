with open('App_original.js', 'r', encoding='utf-8') as f:
    code = f.read()

import re

# Cetak T (Arrears) lengkap
pos_T = code.find('function T(')
pos_ae = code.find('function ae(')
print("=== FUNCTION T (Belum bayar) ===")
print(code[pos_T:pos_ae])

# Cetak ae (Payment) lengkap
pos_oe = code.find('function oe(')
print("=== FUNCTION ae (Catat pembayaran) ===")
print(code[pos_ae:pos_oe])

# Cetak oe (Notifications) lengkap
pos_E = code.find('function E(')
print("=== FUNCTION oe (Notifikasi) ===")
print(code[pos_oe:pos_E])

# Cetak E (Simple page) lengkap
pos_se = code.find('function se(')
print("=== FUNCTION E (Tenants/History/Report) ===")
print(code[pos_E:pos_se])

# Cetak se (App) lengkap
print("=== FUNCTION se (App) ===")
print(code[pos_se:])

