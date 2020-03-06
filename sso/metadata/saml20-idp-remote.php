<?php
/**
 * SAML 2.0 remote IdP metadata for SimpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote
 */

$metadata['https://tenant.ice.ibmcloud.com/saml/sps/saml20ip/saml20'] = array(
    'entityid' => 'https://tenant.ice.ibmcloud.com/saml/sps/saml20ip/saml20',
    'SingleSignOnService'  => 'https://tenant.ice.ibmcloud.com/saml/sps/saml20ip/saml20/login'
    'certificate'          => 'cert.crt',
);
