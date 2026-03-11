import os
import sys

def gestionar_acceso(usuario, rol, ip_vpn):
    print(f"[*] Configurando firewall para {usuario} - Rol: {rol}")
    
    # Limpieza de reglas previas para esta IP (evita duplicados)
    os.system(f"sudo iptables -D FORWARD -s {ip_vpn} -j ACCEPT 2>/dev/null")
    os.system(f"sudo iptables -D FORWARD -s {ip_vpn} -j REJECT 2>/dev/null")

    if rol == "Admin":
        # Acceso total a la Red Completa (Rol: Admin)
        os.system(f"sudo iptables -A FORWARD -s {ip_vpn} -j ACCEPT")
        print("    [+] ACCESO TOTAL CONCEDIDO")
        
    elif rol == "IT":
        # Acceso parcial a infraestructura de red (Rol: IT)
        os.system(f"sudo iptables -A FORWARD -s {ip_vpn} -d 172.18.0.0/16 -j ACCEPT")
        print("    [+] ACCESO LIMITADO A INFRAESTRUCTURA IT")
        
    elif rol == "RRHH":
        # Solo servicios específicos (Web Corporativa) (Rol: RRHH)
        # Suponiendo que la Web está en la IP 172.18.0.10
        os.system(f"sudo iptables -A FORWARD -s {ip_vpn} -d 172.18.0.10 -p tcp --dport 80 -j ACCEPT")
        os.system(f"sudo iptables -A FORWARD -s {ip_vpn} -j REJECT")
        print("    [!] ACCESO RESTRINGIDO A WEB CORPORATIVA. MOVIMIENTO LATERAL BLOQUEADO.")

if __name__ == "__main__":
    if len(sys.argv) < 4:
        print("Uso: python3 rbac_manager.py <USUARIO> <ROL> <IP_VPN>")
        sys.exit(1)
    
    gestionar_acceso(sys.argv[1], sys.argv[2], sys.argv[3])
