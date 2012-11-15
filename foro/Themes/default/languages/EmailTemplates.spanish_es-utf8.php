<?php
// Version: 2.0; EmailTemplates

global $context, $birthdayEmails;

// Important! Before editing these language files please read the text at the top of index.english.php.
// Since all of these strings are being used in emails, numeric entities should be used.
// Do not translate anything that is between {}, they are used as replacement variables and MUST remain exactly how they are.
//   Additionally do not translate the @additioinal_parmas: line or the variable names in the lines that follow it.  You may
//   translate the description of the variable.  Do not translate @description:, however you may translate the rest of that line.
// Do not use block comments in this file, they will have special meaning.
$txt['scheduled_approval_email_topic'] = 'Los siguientes temas están esperando aprobación:';
$txt['scheduled_approval_email_msg'] = 'Los siguientes mensajes están esperando aprobación:';
$txt['scheduled_approval_email_attach'] = 'Los siguientes adjuntos están esperando aprobación:';
$txt['scheduled_approval_email_event'] = 'Los siguientes eventos están esperando aprobación:';

$txt['emails'] = array(
	'resend_activate_message' => array(
		/* 
			@additional_params: resend_activate_message
				REALNAME: The display name for the member receiving the email.
				USERNAME:  The user name for the member receiving the email.
				ACTIVATIONLINK:  The url link to activate the member's account.
				ACTIVATIONCODE:  The code needed to activate the member's account.
			@description: 
		*/
		'subject' => 'Bienvenido/a a {FORUMNAME}',
		'body' => '¡Estás registrado/a en {FORUMNAME}, {REALNAME}!

Tu nombre de usuario es "{USERNAME}".

Antes de que puedas conectarte, primero necesitas activar tu cuenta. Para hacerlo, por favor, pincha en el siguiente enlace:

{ACTIVATIONLINK}

Si tienes problemas con la activación, por favor, utiliza el código "{ACTIVATIONCODE}".

{REGARDS}',
	),

	'resend_pending_message' => array(
		/* 
			@additional_params: resend_pending_message
				REALNAME: The display name for the member receiving the email.
				USERNAME:  The user name for the member receiving the email.
			@description: 
		*/
		'subject' => 'Bienvenido/a a {FORUMNAME}',
		'body' => 'Tu solicitud de registro en {FORUMNAME} ha sido recibida, {REALNAME}.

El usuario que registraste fue {USERNAME}.

Antes de que puedas conectarte y comenzar a utilizar el foro, tu solicitud debe ser revisada y aprobada. Cuando esto suceda, recibirás otro email desde esta dirección.

{REGARDS}',
	),
	'mc_group_approve' => array(
		/*
			@additional_params: mc_group_approve
				USERNAME: The user name for the member receiving the email.
				GROUPNAME: The name of the membergroup that the user was accepted into.
			@description: The request to join a particular membergroup has been accepted.
		*/
		'subject' => 'Aprobación de Pertenencia a Grupo',
		'body' => '{USERNAME},

Nos complace notificarte que tu solicitud para unirte al grupo "{GROUPNAME}" de {FORUMNAME} ha sido aceptada, y tu cuenta ha sido actualizada para incluir este nuevo grupo.

{REGARDS}',
	),
	'mc_group_reject' => array(
		/*
			@additional_params: mc_group_reject
				USERNAME: The user name for the member receiving the email.
				GROUPNAME: The name of the membergroup that the user was rejected from.
			@description: The request to join a particular membergroup has been rejected.
		*/
		'subject' => 'Denegación de Pertenencia a Grupo',
		'body' => '{USERNAME},

Lamentamos notificarte que tu solicitud para unirte al grupo "{GROUPNAME}" de {FORUMNAME} ha sido rechazada.

{REGARDS}',
	),
	'mc_group_reject_reason' => array(
		/*
			@additional_params: mc_group_reject_reason
				USERNAME: The user name for the member receiving the email.
				GROUPNAME: The name of the membergroup that the user was rejected from.
				REASON: Reason for the rejection.
			@description: The request to join a particular membergroup has been rejected with a reason given.
		*/
		'subject' => 'Denegación de Pertenencia a Grupo',
		'body' => '{USERNAME},

Lamentamos notificarte que tu solicitud para unirte al grupo "{GROUPNAME}" de {FORUMNAME} ha sido rechazada.

Es debido a la siguiente razón: {REASON}

{REGARDS}',
	),
	'admin_approve_accept' => array(
		/*
			@additional_params: admin_approve_accept
				NAME: The display name of the member.
				USERNAME: The user name for the member receiving the email.
				PROFILELINK: The URL of the profile page.
			@description:
		*/
		'subject' => 'Bienvenido/a a {FORUMNAME}',
		'body' => 'Bienvenido/a, {USERNAME}!

Tu cuenta ha sido activada manualmente por el administrador y ahora puedes conectarte y publicar mensajes. Tu nombre de usuario es: {USERNAME}

Puedes cambiarlo después de conectarte, yendo a la página del perfil, o visitando esta página después de conectar:
{PROFILELINK}

{REGARDS}',
	),
	'admin_approve_activation' => array(
		/*
			@additional_params: admin_approve_activation
				USERNAME: The user name for the member receiving the email.
				ACTIVATIONLINK:  The url link to activate the member's account.
			@description:
		*/
		'subject' => 'Bienvenido/a a {FORUMNAME}',
		'body' => 'Bienvenido/a, {USERNAME}!

Tu cuenta de {FORUMNAME} ha sido aprobada por el administrador del foro, y debe ser activada ahora antes de que puedas comenzar a publicar mensajes.  Por favor, utiliza el enlace de abajo para activar tu cuenta:
{ACTIVATIONLINK}

{REGARDS}',
	),
	'admin_approve_reject' => array(
		/*
			@additional_params: admin_approve_reject
				USERNAME: The user name for the member receiving the email.
			@description:
		*/
		'subject' => 'Registro Rechazado',
		'body' => '{USERNAME},

Lamentablemente, su solicitud de ingreso en {FORUMNAME} ha sido rechazada.

{REGARDS}',
	),
	'admin_approve_delete' => array(
		/*
			@additional_params: admin_approve_delete
				USERNAME: The user name for the member receiving the email.
			@description:
		*/
		'subject' => 'Cuenta Eliminada',
		'body' => '{USERNAME},

Tu cuenta en {FORUMNAME} ha sido eliminada. Puede ser debido a que nunca activaras tu cuenta, en ese caso podrías volver a registrarte de nuevo.

{REGARDS}',
	),
	'admin_approve_remind' => array(
		/*
			@additional_params: admin_approve_remind
				USERNAME: The user name for the member receiving the email.
				ACTIVATIONLINK:  The url link to activate the member's account.
			@description:
		*/
		'subject' => 'Recordatorio de Registro',
		'body' => '{USERNAME},
Aún no has activado tu cuenta de {FORUMNAME}.

Por favor, utiliza el enlace de abajo para activar tu cuenta:
{ACTIVATIONLINK}

Si tuvieras algún problema con la activación, por favor visita {ACTIVATIONLINKWITHOUTCODE} e introduce el código "{ACTIVATIONCODE}".

{REGARDS}',
	),
	'admin_register_activate' => array(
		/*
			@additional_params:
				USERNAME: El nombre de usuario para el miembro que recibirá el email.
				ACTIVATIONLINK:  La url del enlace para activar la cuenta del usuario.
				ACTIVATIONLINKWITHOUTCODE: La url de la página en la que puede introducirse el código de activación.
				ACTIVATIONCODE: El código de activación.
			@description:
		*/
		'subject' => 'Bienvenido a {FORUMNAME}',
		'body' => 'Gracias por registrarte en {FORUMNAME}. Tu nombre de usuario es {USERNAME} y tu clave {PASSWORD}.

Antes de que puedas acceder, primero debes activar tu cuenta haciendo clic en el soguiente enlace:

{ACTIVATIONLINK}

Si tuvieras algún problema con la activación, por favor visita {ACTIVATIONLINKWITHOUTCODE} e introduce el código "{ACTIVATIONCODE}".

{REGARDS}',
	),
	'admin_register_immediate' => array(
		'subject' => 'Bienvenido a {FORUMNAME}',
		'body' => 'Gracias por registrarte en {FORUMNAME}. Tu nombre de usuario es {USERNAME} y tu contraseña {PASSWORD}.

{REGARDS}',
	),
	'new_announcement' => array(
		/*
			@additional_params: new_announcement
				TOPICSUBJECT: The subject of the topic being announced.
				MESSAGE: The message body of the first post of the announced topic.
				TOPICLINK: A link to the topic being announced.
			@description:

		*/
		'subject' => 'Nuevo Anuncio: {TOPICSUBJECT}',
		'body' => '{MESSAGE}

Para darte de baja de estos Anuncios, conéctate al foro y desmarca la opción "Recibir notificaciones por email cuando haya nuevos Anuncios" de tu perfil.

Puedes ver el mensaje completo en el siguiente enlace:
{TOPICLINK}

{REGARDS}',
	),
	'notify_boards_once_body' => array(
		/*
			@additional_params: notify_boards_once_body
				TOPICSUBJECT: The subject of the topic causing the notification
				TOPICLINK: A link to the topic.
				MESSAGE: This is the body of the message.
				UNSUBSCRIBELINK: Link to unsubscribe from notifications.
			@description:
		*/
		'subject' => 'Nuevo Tema: {TOPICSUBJECT}',
		'body' => 'Se ha creado un nuevo tema, \'{TOPICSUBJECT}\', en un foro que estás observando.

Puedes verlo en
{TOPICLINK}

Pueden haberse publicado más temas, pero no recibirás más notificaciones por email hasta que vayas al foro y leas alguno de ellos.

El texto del tema se muestra abajo:
{MESSAGE}

Para darte de baja de nuevos temas de este foro, utiliza este enlace:
{UNSUBSCRIBELINK}

{REGARDS}',
	),
	'notify_boards_once' => array(
		/*
			@additional_params: notify_boards_once
				TOPICSUBJECT: The subject of the topic causing the notification
				TOPICLINK: A link to the topic.
				UNSUBSCRIBELINK: Link to unsubscribe from notifications.
			@description:
		*/
		'subject' => 'Nuevo Tema: {TOPICSUBJECT}',
		'body' => 'Se ha creado un nuevo tema, \'{TOPICSUBJECT}\', en un foro que estás observando.

Puedes verlo en
{TOPICLINK}

Pueden haberse publicado más temas, pero no recibirás más notificaciones por email hasta que vayas al foro y leas alguno de ellos.

Para darte de baja de nuevos temas de este foro, utiliza este enlace:
{UNSUBSCRIBELINK}

{REGARDS}',
	),
	'notify_boards_body' => array(
		/*
			@additional_params: notify_boards_body
				TOPICSUBJECT: The subject of the topic causing the notification
				TOPICLINK: A link to the topic.
				MESSAGE: This is the body of the message.
				UNSUBSCRIBELINK: Link to unsubscribe from notifications.
			@description:
		*/
		'subject' => 'Nuevo Tema: {TOPICSUBJECT}',
		'body' => 'Se ha creado un nuevo tema, \'{TOPICSUBJECT}\', en un foro que estás observando.

Puedes verlo en
{TOPICLINK}

El texto del tema se muestra abajo:
{MESSAGE}

Para darte de baja de nuevos temas de este foro, utiliza este enlace:
{UNSUBSCRIBELINK}

{REGARDS}',
	),
	'notify_boards' => array(
		/*
			@additional_params: notify_boards
				TOPICSUBJECT: The subject of the topic causing the notification
				TOPICLINK: A link to the topic.
				UNSUBSCRIBELINK: Link to unsubscribe from notifications.
			@description:
		*/
		'subject' => 'Nuevo Tema: {TOPICSUBJECT}',
		'body' => 'Se ha creado un nuevo tema, \'{TOPICSUBJECT}\', en un foro que estás observando.

Puedes verlo en
{TOPICLINK}

Para darte de baja de nuevos temas de este foro, utiliza este enlace:
{UNSUBSCRIBELINK}

{REGARDS}',
	),
	'request_membership' => array(
		/*
			@additional_params: request_membership
				RECPNAME: The name of the person recieving the email
				APPYNAME: The name of the person applying for group membership
				GROUPNAME: The name of the group being applied to.
				REASON: The reason given by the applicant for wanting to join the group.
				MODLINK: Link to the group moderation page.
			@description:
		*/
		'subject' => 'Nueva Solicitud de Ingreso en Grupo',
		'body' => '{RECPNAME},
		
{APPYNAME} ha solicitado una solicitud de ingreso en el grupo "{GROUPNAME}". El usuario ha dado la siguiente razón:

{REASON}

Puedes aprobar o rechazar esta solicitud pulsando en el enlace de abajo:

{MODLINK}

{REGARDS}',
	),
	'paid_subscription_reminder' => array(
		/*
			@additional_params: scheduled_approval
				REALNAME: The real (display) name of the person receiving the email.
				PROFILE_LINK: Link to profile of member receiving email where can renew.
				SUBSCRIPTION: Name of the subscription.
				END_DATE: Date it expires.
			@description:
		*/
		'subject' => 'Subscription about to expire at {FORUMNAME}',
		'body' => '{REALNAME},

Una de tus suscripciones de {FORUMNAME} está a punto de expirar. Si en el momento de darla de alta seleccionó la opción de renovar automáticamente, no es necesario que haga nada - en caso contrario, debe considerar si desea suscribirse de nuevo. A continuación tiene los detalles:

Nombre de la suscripción: {SUBSCRIPTION}
Fecha en la que expira: {END_DATE}

Para editar sus suscripciones visite la siguiente URL:
{PROFILE_LINK}

{REGARDS}',
	),
	'activate_reactivate' => array(
		/*
			@additional_params: activate_reactivate
				ACTIVATIONLINK:  The url link to reactivate the member's account.
				ACTIVATIONCODE:  The code needed to reactivate the member's account.
			@description: 
		*/
		'subject' => 'Bienvenido de nuevo a {FORUMNAME}',
		'body' => 'Para revalidar tu dirección email, tu cuenta ha sido desactivada. Pulsa en el enlace siguiente para volverla a activar:
{ACTIVATIONLINK}

Si tienes problemas con la activación, por favor, utiliza el código "{ACTIVATIONCODE}".

{REGARDS}',
	),
	'forgot_password' => array(
		/*
			@additional_params: forgot_password
				REALNAME: The real (display) name of the person receiving the reminder.
				REMINDLINK: The link to reset the password.
				IP: The IP address of the requester.
				MEMBERNAME: 
			@description: 
		*/
		'subject' => 'Nueva contraseña para {FORUMNAME}',
		'body' => 'Estimado/a {REALNAME},
Se ha enviado este correo porque se ha aplicado la función \'contraseña olvidada\' en tu cuenta. Para establecer una contraseña nueva, pulsa en el siguiente enlace:
{REMINDLINK}

IP: {IP}
Usuario: {MEMBERNAME}

{REGARDS}',
	),
	'forgot_openid' => array(
		/*
			@additional_params: forgot_password
				REALNAME: The real (display) name of the person receiving the reminder.
				IP: The IP address of the requester.
				OPENID: The members OpenID identity.
			@description:
		*/
		'subject' => 'Recordatorio de OpenID para {FORUMNAME}',
		'body' => 'Estimado/a {REALNAME},
Este correo fue enviado porque se activó la función \'olvidé mi OpenID\' para su cuenta. A continuación tiene la OpenID a la que está asociada su cuenta de usuario:
{OPENID}

IP: {IP}
Nombre de usuario: {MEMBERNAME}

{REGARDS}',
	),
	'scheduled_approval' => array(
		/*
			@additional_params: scheduled_approval
				REALNAME: The real (display) name of the person receiving the email.
				BODY: The generated body of the mail.
			@description:
		*/
		'subject' => 'Resumen de mensajes esperando aprobación en {FORUMNAME}',
		'body' => '{REALNAME},
		
Este email contiene un resumen de todos los elementos esperando aprobación en {FORUMNAME}.

{BODY}

Por favor, conéctate al foro para revisar estos elementos.
{SCRIPTURL}

{REGARDS}'
	),
	'send_topic' => array(
		/*
			@additional_params: send_topic
				TOPICSUBJECT: The subject of the topic being sent.
				SENDERNAME: The name of the member sending the topic.
				RECPNAME: The name of the person receiving the email.
				TOPICLINK: A link to the topic being sent.
			@description:
		*/
		'subject' => 'Tema: {TOPICSUBJECT} (De: {SENDERNAME})',
		'body' => 'Estimado/a {RECPNAME},
Me gustaría que comprobaras "{TOPICSUBJECT}" de {FORUMNAME}.  Para verlo, por favor, pulsa en este enlace:

{TOPICLINK}

Gracias,

{SENDERNAME}',
	),
	'send_topic_comment' => array(
		/*
			@additional_params: send_topic_comment
				TOPICSUBJECT: The subject of the topic being sent.
				SENDERNAME: The name of the member sending the topic.
				RECPNAME: The name of the person receiving the email.
				TOPICLINK: A link to the topic being sent.
				COMMENT: A comment left by the sender.
			@description:
		*/
		'subject' => 'Tema: {TOPICSUBJECT} (De: {SENDERNAME})',
		'body' => 'Estimado/a {RECPNAME},
Me gustaría que comprobaras "{TOPICSUBJECT}" de {FORUMNAME}.  Para verlo, por favor, pulsa en este enlace:

{TOPICLINK}

Se ha añadido también un comentario referente a este tema:
{COMMENT}

Gracias,

{SENDERNAME}',
	),
	'send_email' => array(
		/*
			@additional_params: send_email
				EMAILSUBJECT: The subject the user wants to email.
				EMAILBODY: The body the user wants to email.
				SENDERNAME: The name of the member sending the email.
				RECPNAME: The name of the person receiving the email.
			@description:
		*/
		'subject' => '{EMAILSUBJECT}',
		'body' => '{EMAILBODY}',
	),
	'report_to_moderator' => array(
		/* 
			@additional_params: report_to_moderator
				TOPICSUBJECT: The subject of the reported post.
				POSTERNAME: The report post's author's name.
				REPORTERNAME: The name of the person reporting the post.
				TOPICLINK: The url of the post that is being reported.
				REPORTLINK: The url of the moderation center report.
				COMMENT: The comment left by the reporter, hopefully to explain why they are reporting the post.
			@description: When a user reports a post this email is sent out to moderators and admins of that board.
		*/
		'subject' => 'Mensaje Informado: {TOPICSUBJECT} de {POSTERNAME}',
		'body' => 'El siguiente mensaje, "{TOPICSUBJECT}" de {POSTERNAME} ha sido informado por {REPORTERNAME} en un foro que tú moderas:

El tema: {TOPICLINK}
Centro de Moderación: {REPORTLINK}

El informador ha realizado el siguiente comentario:
{COMMENT}

{REGARDS}',
	),
	'change_password' => array(
		/*
			@additional_params: change_password
				USERNAME: The user name for the member receiving the email.
				PASSWORD: The password for the member.
			@description:
		*/
		'subject' => 'Detalles de la Nueva Contraseña',
		'body' => '¡Hola {USERNAME}!

Los detalles de tu conexión a {FORUMNAME} han sido cambiados y tu contraseña reseteada. Abajo tienes tus nuevos detalles de conexión.

Tu nombre de usuario es "{USERNAME}" y tu contraseña es "{PASSWORD}".

Puedes cambiarlos después de conectar yendo a tu página de perfil, o visitando esta página después de conectar:
{SCRIPTURL}?action=profile

{REGARDS}',
	),
	'register_activate' => array(
		/*
			@additional_params: register_activate
				REALNAME: The display name for the member receiving the email.
				USERNAME: The user name for the member receiving the email.
				PASSWORD: The password for the member.
				ACTIVATIONLINK:  The url link to reactivate the member's account.
				ACTIVATIONCODE:  The code needed to reactivate the member's account.
			@description:
		*/
		'subject' => 'Bienvenido/a a {FORUMNAME}',
		'body' => '¡Estás registrado en {FORUMNAME}, {REALNAME}!

Tu nombre de usuario de la cuenta es {USERNAME} y su contraseña es {PASSWORD} (que puede cambiarse posteriormente.)

Antes de que puedas conectarte, primero necesitas activar tu cuenta. Para hacerlo, por favor, entra en este enlace:

{ACTIVATIONLINK}

Si tienes problemas con la activación, por favor, utiliza el código "{ACTIVATIONCODE}".

{REGARDS}',
	),
	'register_openid_activate' => array(
		/*
			@additional_params: register_activate
				REALNAME: The display name for the member receiving the email.
				USERNAME: The user name for the member receiving the email.
				OPENID: The openID identity for the member.
				ACTIVATIONLINK:  The url link to reactivate the member's account.
				ACTIVATIONCODE:  The code needed to reactivate the member's account.
			@description:
		*/
		'subject' => 'Bienvenido/a a {FORUMNAME}',
		'body' => 'Acaba de registrar una cuenta en {FORUMNAME}, {REALNAME}!

Su nombre de usuario para su cuenta es {USERNAME}. Ha elegido autenticarse usando la siguiente identidad OpenID:
{OPENID}

Antes de que pueda acceder, primero debe activar su cuenta. Para hacerlo, por favor, siga este enlace:

{ACTIVATIONLINK}

Si tuviera algún problema con la activación, por favor utilice el código "{ACTIVATIONCODE}".

{REGARDS}',
	),
	'register_coppa' => array(
		/*
			@additional_params: register_coppa
				REALNAME: The display name for the member receiving the email.
				USERNAME: The user name for the member receiving the email.
				PASSWORD: The password for the member.
				COPPALINK:  The url link to the coppa form.
			@description:
		*/
		'subject' => 'Welcome to {FORUMNAME}',
		'body' => 'You are now registered with an account at {FORUMNAME}, {REALNAME}!

Your account\'s username is {USERNAME} and its password is {PASSWORD} (which can be changed later.)

Before you can login, the admin requires consent from your parent/guardian for you to join the community. You can obtain more information at the link below:

{COPPALINK}

{REGARDS}',
	),
	'register_openid_coppa' => array(
		/*
			@additional_params: register_coppa
				REALNAME: The display name for the member receiving the email.
				USERNAME: The user name for the member receiving the email.
				OPENID: The openID identity for the member.
				COPPALINK:  The url link to the coppa form.
			@description:
		*/
		'subject' => 'Welcome to {FORUMNAME}',
		'body' => 'You are now registered with an account at {FORUMNAME}, {REALNAME}!

Your account\'s username is {USERNAME}.

You have chosen to authenticate using the following OpenID identity:
{OPENID}

Before you can login, the admin requires consent from your parent/guardian for you to join the community. You can obtain more information at the link below:

{COPPALINK}

{REGARDS}',
	),
	'register_immediate' => array(
		/*
			@additional_params: register_immediate
				REALNAME: The display name for the member receiving the email.
				USERNAME: The user name for the member receiving the email.
				PASSWORD: The password for the member.
			@description:
		*/
		'subject' => 'Bienvenido/a a {FORUMNAME}',
		'body' => '¡Estás registrado en {FORUMNAME}, {REALNAME}!

Tu nombre de usuario de la cuenta es {USERNAME} y su contraseña es {PASSWORD}.

Puedes cambiar tu contraseña después de conectar yendo a tu perfil, o visitando esta página después de conectar:

{SCRIPTURL}?action=profile

{REGARDS}',
	),
	'register_openid_immediate' => array(
		/*
			@additional_params: register_immediate
				REALNAME: The display name for the member receiving the email.
				USERNAME: The user name for the member receiving the email.
				OPENID: The openID identity for the member.
			@description:
		*/
		'subject' => 'Welcome to {FORUMNAME}',
		'body' => 'You are now registered with an account at {FORUMNAME}, {REALNAME}!

Your account\'s username is {USERNAME}.

You have chosen to authenticate using the following OpenID identity:
{OPENID}

You may update your profile by visiting this page after you login:

{SCRIPTURL}?action=profile

{REGARDS}',
	),
	'register_pending' => array(
		/*
			@additional_params: register_pending
				REALNAME: The display name for the member receiving the email.
				USERNAME: The user name for the member receiving the email.
				PASSWORD: The password for the member.
			@description:
		*/
		'subject' => 'Bienvenido/a a {FORUMNAME}',
		'body' => 'Se ha recibido tu solicitud de registro en {FORUMNAME}, {REALNAME}.

El usuario que registraste fue {USERNAME} y su contraseña fue {PASSWORD}.

Antes de que puedas conectarte y empezar a utilizar el foro, tu solicitud debe ser revisada y aprobada. Cuando esto suceda, recibirás otro email desde esta dirección.

{REGARDS}',
	),
	'register_openid_pending' => array(
		/*
			@additional_params: register_pending
				REALNAME: The display name for the member receiving the email.
				USERNAME: The user name for the member receiving the email.
				OPENID: The openID identity for the member.
			@description:
		*/
		'subject' => 'Welcome to {FORUMNAME}',
		'body' => 'Your registration request at {FORUMNAME} has been received, {REALNAME}.

The username you registered with was {USERNAME}.

You have chosen to authenticate using the following OpenID identity:
{OPENID}

Before you can login and start using the forum, your request will be reviewed and approved.  When this happens, you will receive another email from this address.

{REGARDS}',
	),
	'notification_reply' => array(
		/*
			@additional_params: notification_reply
				TOPICSUBJECT:
				POSTERNAME:
				TOPICLINK:
				UNSUBSCRIBELINK:
			@description:
		*/
		'subject' => 'Respuesta a Tema: {TOPICSUBJECT}',
		'body' => 'Se ha publicado una respuesta a un tema de {POSTERNAME} que estás observando.

Puedes ver la respuesta en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

{REGARDS}',
	),
	'notification_reply_body' => array(
		/*
			@additional_params: notification_reply_body
				TOPICSUBJECT:
				POSTERNAME:
				TOPICLINK:
				UNSUBSCRIBELINK:
				MESSAGE: 
			@description:
		*/
		'subject' => 'Respuesta a Tema: {TOPICSUBJECT}',
		'body' => 'Se ha publicado una respuesta a un tema de {POSTERNAME} que estás observando.

Puedes ver la respuesta en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

La respuesta se muestra abajo:
{MESSAGE}

{REGARDS}',
	),
	'notification_reply_once' => array(
		/*
			@additional_params: notification_reply_once
				TOPICSUBJECT:
				POSTERNAME:
				TOPICLINK:
				UNSUBSCRIBELINK:
			@description:
		*/
		'subject' => 'Respuesta a Tema: {TOPICSUBJECT}',
		'body' => 'Se ha publicado una respuesta a un tema de {POSTERNAME} que estás observando.

Puedes ver la respuesta en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

Pueden haberse publicado más respuestas, pero no recibirás más notificaciones hasta que leas el tema.

{REGARDS}',
	),
	'notification_reply_body_once' => array(
		/*
			@additional_params: notification_reply_body_once
				TOPICSUBJECT:
				POSTERNAME:
				TOPICLINK:
				UNSUBSCRIBELINK:
				MESSAGE: 
			@description:
		*/
		'subject' => 'Respuesta a Tema: {TOPICSUBJECT}',
		'body' => 'Se ha publicado una respuesta a un tema de {POSTERNAME} que estás observando.

Puedes ver la respuesta en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

La respuesta se muestra abajo:
{MESSAGE}

Pueden haberse publicado más respuestas, pero no recibirás más notificaciones hasta que leas el tema.

{REGARDS}',
	),
	'notification_sticky' => array(
		/*
			@additional_params: notification_sticky
			@description:
		*/
		'subject' => 'Tema fijado: {TOPICSUBJECT}',
		'body' => 'Un tema que estás observando ha sido fijado con chincheta por {POSTERNAME}.

Puedes ver el tema en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

{REGARDS}',
	),
	'notification_lock' => array(
		/*
			@additional_params: notification_lock
			@description:
		*/
		'subject' => 'Tema Bloqueado: {TOPICSUBJECT}',
		'body' => 'Un tema que estás observando ha sido bloqueado por {POSTERNAME}.

Puedes ver el tema en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

{REGARDS}',
	),
	'notification_unlock' => array(
		/*
			@additional_params: notification_unlock
			@description:
		*/
		'subject' => 'Tema desbloqueado: {TOPICSUBJECT}',
		'body' => 'Un tema que estás observando ha sido desbloqueado por {POSTERNAME}.

Puedes ver el tema en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

{REGARDS}',
	),
	'notification_remove' => array(
		/*
			@additional_params: notification_remove
			@description:
		*/
		'subject' => 'Tema eliminado: {TOPICSUBJECT}',
		'body' => 'Un tema que estás observando ha sido eliminado por {POSTERNAME}.

{REGARDS}',
	),
	'notification_move' => array(
		/*
			@additional_params: notification_move
			@description:
		*/
		'subject' => 'Topic movido: {TOPICSUBJECT}',
		'body' => 'Un tema que estás observando ha sido movido a otro foro por {POSTERNAME}.

Puedes ver el tema en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

{REGARDS}',
	),
	'notification_merge' => array(
		/*
			@additional_params: notification_merged
			@description:
		*/
		'subject' => 'Tema combinado: {TOPICSUBJECT}',
		'body' => 'Un tema que estás observando ha sido combinado con otro tema por {POSTERNAME}.

Puedes ver el nuevo tema combinado en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

{REGARDS}',
	),
	'notification_split' => array(
		/*
			@additional_params: notification_split
			@description:
		*/
		'subject' => 'Tema dividido: {TOPICSUBJECT}',
		'body' => 'Un tema que estás observando ha sido dividido en dos o más temas por {POSTERNAME}.

Puedes ver lo que queda de este tema en: {TOPICLINK}

Para darte de baja de este tema utiliza este enlace: {UNSUBSCRIBELINK}

{REGARDS}',
	),
	'admin_notify' => array(
		/*
			@additional_params: admin_notify
				USERNAME: 
				PROFILELINK: 
			@description:
		*/
		'subject' => 'Se ha registrado un nuevo usuario',
		'body' => '{USERNAME} acaba de registrarse como nuevo usuario de tu foro. Pulsa en el enlace de abajo para ver su perfil.
{PROFILELINK}

{REGARDS}',
	),
	'admin_notify_approval' => array(
		/*
			@additional_params: admin_notify_approval
				USERNAME: 
				PROFILELINK: 
				APPROVALLINK: 
			@description:
		*/
		'subject' => 'Se ha registrado un nuevo usuario',
		'body' => '{USERNAME} acaba de registrarse como nuevo usuario de tu foro. Pulsa en el enlace de abajo para ver su perfil.
{PROFILELINK}

Antes de que este usuario pueda comenzar a publicar mensajes, debe aprobarse su cuenta. Pulsa en el enlace de abajo para ir a la pantalla de aprobaciones.
{APPROVALLINK}

{REGARDS}',
	),
	'admin_attachments_full' => array(
		/*
			@additional_params: admin_attachments_full
				REALNAME:
			@description:
		*/
		'subject' => 'Urgent! Attachments folder almost full',
		'body' => '{REALNAME},

The attachments folder at {FORUMNAME} is almost full. Please visit the forum to resolve this problem.

Once the attachments folder reaches it\'s maximum permitted size users will not be able to continue to post attachments or upload custom avatars (If enabled).

{REGARDS}',
	),
	'paid_subscription_refund' => array(
		/*
			@additional_params: paid_subscription_refund
				NAME: Subscription title.
				REALNAME: Recipients name
				REFUNDUSER: Username who took out the subscription.
				REFUNDNAME: User's display name who took out the subscription.
				DATE: Today's date.
				PROFILELINK: Link to members profile.
			@description:
		*/
		'subject' => 'Refunded Paid Subscription',
		'body' => '{REALNAME},

A member has received a refund on a paid subscription. Below are the details of this subscription:

	Subscription: {NAME}
	User Name: {REFUNDNAME} ({REFUNDUSER})
	Date: {DATE}

You can view this members profile by clicking the link below:
{PROFILELINK}

{REGARDS}',
	),
	'paid_subscription_new' => array(
		/*
			@additional_params: paid_subscription_new
				NAME: Subscription title.
				REALNAME: Recipients name
				SUBEMAIL: Email address of the user who took out the subscription
				SUBUSER: Username who took out the subscription.
				SUBNAME: User's display name who took out the subscription.
				DATE: Today's date.
				PROFILELINK: Link to members profile.
			@description:
		*/
		'subject' => 'New Paid Subscription',
		'body' => '{REALNAME},

A member has taken out a new paid subscription. Below are the details of this subscription:

	Subscription: {NAME}
	User Name: {SUBNAME} ({SUBUSER})
	User Email: {SUBEMAIL}
	Price: {PRICE}
	Date: {DATE}

You can view this members profile by clicking the link below:
{PROFILELINK}

{REGARDS}',
	),
	'paid_subscription_error' => array(
		/*
			@additional_params: paid_subscription_error
				ERROR: Error message.
				REALNAME: Recipients name
			@description:
		*/
		'subject' => 'Paid Subscription Error Occured',
		'body' => '{REALNAME},

The following error occured when processing a paid subscription
---------------------------------------------------------------
{ERROR}

{REGARDS}',
	),
);

/*
	@additional_params: happy_birthday
		REALNAME: The real (display) name of the person receiving the birthday message.
	@description: A message sent to members on their birthday.
*/
$birthdayEmails = array(
	'happy_birthday' => array(
		'subject' => 'Happy birthday from {FORUMNAME}.',
		'body' => 'Dear {REALNAME},

We here at {FORUMNAME} would like to wish you a happy birthday.  May this day and the year to follow be full of joy.

{REGARDS}',
		'author' => '<a href="http://www.simplemachines.org/community/?action=profile;u=2676">Thantos</a>',
	),
	'karlbenson1' => array(
		'subject' => 'On your Birthday...',
		'body' => 'We could have sent you a birthday card.  We could have sent you some flowers or a cake.

But we didn\'t.

We could have even sent you one of those automatically generated messages to wish you happy birthday where we don\'t even have to replace INSERT NAME.

But we didn\'t

We wrote this birthday greeting just for you.

We would like to wish you a very special birthday.

{REGARDS}

//:: This message was automatically generated :://',
		'author' => '<a href="http://www.simplemachines.org/community/?action=profile;u=63186">karlbenson</a>',
	),
	'nite0859' => array(
		'subject' => 'Happy Birthday!',
		'body' => 'Your friends at {FORUMNAME} would like to take a moment of your time to wish you a happy birthday, {REALNAME}. If you have not done so recently, please visit our community in order for others to have the opportunity to pass along their warm regards.

Even though today is your birthday, {REALNAME}, we would like to remind you that your membership in our community has been the best gift to us thus far.

Best Wishes,
The Staff of {FORUMNAME}',
		'author' => '<a href="http://www.simplemachines.org/community/?action=profile;u=46625">nite0859</a>',
	),
	'zwaldowski' => array(
		'subject' => 'Birthday Wishes to {REALNAME}',
		'body' => 'Dear {REALNAME},

Another year in your life has passed.  We at {FORUMNAME} hope it has been filled with happiness, and wish you luck in the coming one.

{REGARDS}',
		'author' => '<a href="http://www.simplemachines.org/community/?action=profile;u=72038">zwaldowski</a>',
	),
	'geezmo' => array(
		'subject' => 'Happy birthday, {REALNAME}!',
		'body' => 'Do you know who\'s having a birthday today, {REALNAME}?

We know... YOU!

Happy birthday!

You\'re now a year older but we hope you\'re a lot happier than last year.

Enjoy your day today, {REALNAME}!

- From your {FORUMNAME} family',
		'author' => '<a href="http://www.simplemachines.org/community/?action=profile;u=48671">geezmo</a>',
	),
	'karlbenson2' => array(
		'subject' => 'Your Birthday Greeting',
		'body' => 'We hope your birthday is the best ever cloudly, sunny or whatever the weather.
Have lots of birthday cake and fun, and tell us what you have done.

We hope this message brought you cheer, and make it last, until same time same place, next year.

{REGARDS}',
		'author' => '<a href="http://www.simplemachines.org/community/?action=profile;u=63186">karlbenson</a>',
	),
);

?>