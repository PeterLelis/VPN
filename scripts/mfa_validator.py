import pyotp
import sys

def validar_token(secreto_usuario, codigo_introducido):
    # Inicializa el objeto TOTP con el secreto guardado
    totp = pyotp.TOTP(secreto_usuario)
    # Verifica si el código es válido en la ventana de tiempo actual
    return totp.verify(codigo_introducido)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Uso: python3 mfa_validator.py <SECRETO> <CODIGO>")
        sys.exit(1)

    secreto = sys.argv[1]
    codigo = sys.argv[2]

    if validar_token(secreto, codigo):
        print("AUTENTICACIÓN MFA EXITOSA")
        sys.exit(0)
    else:
        print("ERROR: CÓDIGO MFA INVÁLIDO O EXPIRADO")
        sys.exit(1)
