<?php


define('CUSTOMER_GUARD', 'customer-api');

/**
 * super admin guard
 *
 * @author WeSSaM
 */
define('ADMIN_GUARD', 'admin-api');



/***
 *
 */
define('TECHNICIAN_GUARD', 'technician-api');
/**
 * login transaction
 *
 * @author WeSSaM
 */
define('LOGIN_TRANSACTION', 10001);

/**
 * logout transaction
 *
 * @author WeSSaM
 */
define('LOGOUT_TRANSACTION', 10002);

/**
 * this is the key of error
 * response
 *
 * @author WeSSaM
 */
define('ERROR_RESPONSE', false);
/**
 * this is the key of success
 * response
 *
 * @author WeSSaM
 */
define('SUCCESS_RESPONSE', true);
/**
 * Http success status
 *
 * @author WeSSaM
 */
define('SUCCESS_STATUS', true);




define('FAILURE_STATUS', false);


/**
 * INACTIVE status
 *
 * @author WeSSaM
 */
define('INACTIVE', 0);
/**
 * ACTIVE status
 *
 * @author WeSSaM
 */
define('ACTIVE', 1);

/**
 * SUCCESS STATUS
 *
 * @author Wessam
 */
define('SUCCESS', 1);

/**
 * ERROR STATUS
 *
 * @author Wessam
 */
define('ERROR', 0);

define('ENTERNAL_ERROR', 500);

/**
 * error : not found
 * Http status code
 *
 * @author WeSSaM
 */
define('USER_NOT_FOUND', 100);
/**
 * error:  user is not active
 * Http status code
 *
 * @author WeSSaM
 */
define('USER_NOT_ACTIVE', 101);
/**
 * error : not authorized access
 * Http status code
 *
 * @author WeSSaM
 */
define('NOT_AUTHORIZED_ACCESS', 102);
/**
 * error : validation exception
 * Http status code
 *
 * @author WeSSaM
 */
define('VALIDATION_EXCEPTION', 103);
/**
 * error : crud error
 * Http status code
 */
define('RESOURCE_MANIPULATION', 103);
/**
 * error : could not delete the resource 'cause it has
 * children
 * Http status code
 */
define('UNAUTHENTICATED_ERROR', 106);
/**
 * error : unknown database
 * Http status code
 */
define('UNKNOWN_DATABASE', 105);

/**
 * error : UNKNOWN_RELATION
 * Http status code
 */
define('UNKNOWN_RELATION', 107);

/**
 * error : DATABASE_CONNECTION_ERROR
 * Http status code
 */
define('DATABASE_CONNECTION_ERROR', 112);

/**
 * error :  INVALID INPUT
 * Http status code
 */
define('INVALID_INPUT', 106);

/**
 * error : database backup
 * Http status code
 */
define('BACKUP_ERROR', 1105);
/**
 * error : resource not found
 * Http status code
 */
define('RESOURCE_NOT_FOUND', 404);
/**
 * error : could not delete the resource 'cause it has
 * children
 * Http status code
 */
define('DELETE_CHILDREN_ERROR', 1106);
/**
 * error : invalid access code
 *
 * Http status code
 */
define('INVALID_ACCESS_CODE', 1107);
/**
 * error : error while creating database
 *
 * Http status code
 */
define('CREATE_DATABASE_ERROR', 1108);
/**
 *
 * error : INVALID TOKEN
 *
 * Http status code
 */
define('INVALID_TOKEN', 1109);

/**
 *
 * error : UPLOADING_ERROR
 *
 * Http status code
 */
define('UPLOADING_ERROR', 408);

/**
 *
 * error : ADMIN_TOPIC
 *
 */
define('ADMIN_TOPIC', 'admins');

/**
 *
 * error : ADMIN_TOPIC
 *
 */
define('CUSTOMER_TOPIC', 'customers');
/***
 * TECHNICIAN_TOPIC
 */

define('TECHNICIAN_TOPIC', 'technicians');
/***
 * STATUS_TICKET
 */
const STATUS_TICKET = 'ticket';
/***
 * APPOINTMENT_STATUES
 */
const APPOINTMENT_STATUES = 'appointment';
/***
 * CUSTOMER_PRICING_STATUES
 */
const CUSTOMER_PRICING_STATUES = 'customer_pricing';
/***
 * QUOTATION_STATUES
 */
const QUOTATION_STATUES = 'quotation';
/***
 * REJECTED_STATUES
 */
const REJECTED_STATUES = 'rejected';
/***
 * PART_PRICING_STATUES
 */
const PART_PRICING_STATUES = 'part_pricing';
