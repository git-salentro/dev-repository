<?php

namespace Erp\CoreBundle\EmailNotification;

use Erp\CoreBundle\EmailNotification\Notifications\AdminUserCreate;
use Erp\CoreBundle\EmailNotification\Notifications\ApplicationFormInstruction;
use Erp\CoreBundle\EmailNotification\Notifications\AppointmentRequest;
use Erp\CoreBundle\EmailNotification\Notifications\InviteTenantUser;
use Erp\CoreBundle\EmailNotification\Notifications\AssignTenantUser;
use Erp\CoreBundle\EmailNotification\Notifications\LandlordCompleteProfile;
use Erp\CoreBundle\EmailNotification\Notifications\LandlordUserRegister;
use Erp\CoreBundle\EmailNotification\Notifications\PaySimpleBankAccount;
use Erp\CoreBundle\EmailNotification\Notifications\PaySimpleCheckError;
use Erp\CoreBundle\EmailNotification\Notifications\SmartMoveCheckUser;
use Erp\CoreBundle\EmailNotification\Notifications\UserChangeEmail;
use Erp\CoreBundle\EmailNotification\Notifications\UserPasswordResetting;
use Erp\CoreBundle\EmailNotification\Notifications\LandlordInvite;
use Erp\CoreBundle\EmailNotification\Notifications\LandlordActiveInvite;
use Erp\CoreBundle\EmailNotification\Notifications\UserSetting;
use Erp\CoreBundle\EmailNotification\Notifications\UserDeactivate;
use Erp\CoreBundle\EmailNotification\Notifications\UserActivate;
use Erp\CoreBundle\EmailNotification\Notifications\ApplicationFormToLandlord;
use Erp\CoreBundle\EmailNotification\Notifications\ContactFormToAdmin;

class EmailNotificationFactory
{
    const TYPE_ADMIN_USER_CREATE            = 'admin_user_create';
    const TYPE_LANDLORD_USER_REGISTER       = 'landlors_user_register';
    const TYPE_LANDLORD_COMPLETE_PROFILE    = 'landlors_complete_profile';
    const TYPE_USER_PASSWORD_RESETTING      = 'user_password_resetting_mail';
    const TYPE_APPOINTMENT_REQUEST          = 'appointment_request';
    const TYPE_LANDLORD_INVITE              = 'landlord_invite';
    const TYPE_LANDLORD_ACTIVE_INVITE       = 'landlord_active_invite';
    const TYPE_USER_SETTING                 = 'user_setting';
    const TYPE_PS_CHECK_ERROR               = 'ps_check_error';
    const TYPE_INVITE_TENANT_USER           = 'invite_tenant_user';
    const TYPE_ASSIGN_TENANT_USER           = 'assign_tenant_user';
    const TYPE_USER_DEACTIVATE              = 'user_deactivate';
    const TYPE_APPLICATION_FORM_TO_LANDLORD = 'application_form_to_landlord';
    const TYPE_APPLICATION_FORM_INSTRUCTION = 'application_form_instruction';
    const TYPE_CONTACT_FORM_TO_ADMIN        = 'contact_form_to_admin';
    const TYPE_SM_CHECK_USER                = 'smart_move_user_check';
    const TYPE_PS_BANK_ACCOUNT              = 'ps_bank_account';

    /**
     * Get email provider by type
     *
     * @param $type
     *
     * @return AdminUserCreate
     *
     * @throws EmailNotificationException
     */
    public static function getProvider($type)
    {
        switch ($type) {
            case self::TYPE_ADMIN_USER_CREATE:
                $provider = new AdminUserCreate();
                break;
            case self::TYPE_LANDLORD_USER_REGISTER:
                $provider = new LandlordUserRegister();
                break;
            case self::TYPE_LANDLORD_COMPLETE_PROFILE:
                $provider = new LandlordCompleteProfile();
                break;
            case self::TYPE_USER_PASSWORD_RESETTING:
                $provider = new UserPasswordResetting();
                break;
            case self::TYPE_APPOINTMENT_REQUEST:
                $provider = new AppointmentRequest();
                break;
            case self::TYPE_LANDLORD_INVITE:
                $provider = new LandlordInvite();
                break;
            case self::TYPE_LANDLORD_ACTIVE_INVITE:
                $provider = new LandlordActiveInvite();
                break;
            case self::TYPE_USER_SETTING:
                $provider = new UserSetting();
                break;
            case self::TYPE_PS_CHECK_ERROR:
                $provider = new PaySimpleCheckError();
                break;
            case self::TYPE_INVITE_TENANT_USER:
                $provider = new InviteTenantUser();
                break;
            case self::TYPE_ASSIGN_TENANT_USER:
                $provider = new AssignTenantUser();
                break;
            case self::TYPE_USER_DEACTIVATE:
                $provider = new UserDeactivate();
                break;
            case self::TYPE_APPLICATION_FORM_TO_LANDLORD:
                $provider = new ApplicationFormToLandlord();
                break;
            case self::TYPE_CONTACT_FORM_TO_ADMIN:
                $provider = new ContactFormToAdmin();
                break;
            case self::TYPE_SM_CHECK_USER:
                $provider = new SmartMoveCheckUser();
                break;
            case self::TYPE_PS_BANK_ACCOUNT:
                $provider = new PaySimpleBankAccount();
                break;
            case self::TYPE_APPLICATION_FORM_INSTRUCTION:
                $provider = new ApplicationFormInstruction();
                break;
            default:
                $available = [
                    self::TYPE_ADMIN_USER_CREATE,
                    self::TYPE_LANDLORD_USER_REGISTER,
                    self::TYPE_LANDLORD_COMPLETE_PROFILE,
                    self::TYPE_USER_PASSWORD_RESETTING,
                    self::TYPE_APPOINTMENT_REQUEST,
                    self::TYPE_LANDLORD_INVITE,
                    self::TYPE_LANDLORD_ACTIVE_INVITE,
                    self::TYPE_USER_SETTING,
                    self::TYPE_PS_CHECK_ERROR,
                    self::TYPE_INVITE_TENANT_USER,
                    self::TYPE_ASSIGN_TENANT_USER,
                    self::TYPE_USER_DEACTIVATE,
                    self::TYPE_APPLICATION_FORM_TO_LANDLORD,
                    self::TYPE_CONTACT_FORM_TO_ADMIN,
                    self::TYPE_SM_CHECK_USER,
                    self::TYPE_PS_BANK_ACCOUNT,
                    self::TYPE_APPLICATION_FORM_INSTRUCTION,
                ];
                throw new EmailNotificationException(
                    sprintf(
                        'Email notification adapter %s not found. Available notifications: %s',
                        $type,
                        implode(', ', $available)
                    )
                );
                break;
        }

        return $provider;
    }
}
