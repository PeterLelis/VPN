<?php
// auth/ldap_mgmt.php

function crear_usuario_ldap($uid, $nombre, $apellido, $password) {
    $ldap_host = "ldap-server"; // Nombre del servicio en Docker
    $ldap_dn   = "cn=admin,dc=percyjackson,dc=security";
    $ldap_pass = "Admin01";
    $base_dn   = "ou=users,dc=percyjackson,dc=security";

    $ds = ldap_connect($ldap_host);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

    if ($ds) {
        // Autenticarse como admin para poder escribir
        $r = ldap_bind($ds, $ldap_dn, $ldap_pass);
        
        if (!$r) return false;

        // Preparar el array de atributos para LDAP
        $info["cn"] = $nombre . " " . $apellido;
        $info["sn"] = $apellido;
        $info["givenName"] = $nombre;
        $info["uid"] = $uid;
        $info["userPassword"] = $password; // LDAP gestionará el hash o texto plano
        $info["objectclass"][0] = "top";
        $info["objectclass"][1] = "person";
        $info["objectclass"][2] = "organizationalPerson";
        $info["objectclass"][3] = "inetOrgPerson";

        // Intentar añadir el registro
       // $dn_nuevo = "uid=$uid,$base_dn";
       $dn_nuevo = "cn=$nombre,$base_dn";
        $resultado = @ldap_add($ds, $dn_nuevo, $info);
        
        ldap_close($ds);
        return $resultado;
    }
    return false;
}
