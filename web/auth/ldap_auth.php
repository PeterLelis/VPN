<?php
require_once __DIR__ . '/../config/config.php';

function autenticar_ldap($user, $pass) {
    $ldap_conn = ldap_connect(LDAP_HOST, LDAP_PORT);
    if (!$ldap_conn) return ['status' => false];

    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

    // DN exacto de Percy: cn=percy,ou=users,dc=percyjackson,dc=security
    $user_dn = "cn=" . $user . "," . LDAP_USER_OU;

    // Intentamos el Bind (Apretón de manos de seguridad)
    if (@ldap_bind($ldap_conn, $user_dn, $pass)) {
        // Obtenemos los atributos para el Dashboard (RBAC) y el MFA
        $search = @ldap_read($ldap_conn, $user_dn, "(objectClass=*)", array("gidNumber", "description"));
        
        if ($search) {
            $info = ldap_get_entries($ldap_conn, $search);
            if ($info["count"] > 0) {
                return [
                    'status' => true, 
                    'rol' => $info[0]['gidnumber'][0] ?? '5002', 
                    'mfa_secret' => $info[0]['description'][0] ?? null 
                ];
            }
        }
    }
    return ['status' => false];
}