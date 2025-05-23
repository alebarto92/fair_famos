<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit411781ce819b3aacaf65c92040d2f340
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        'a0edc8309cc5e1d60e3047b5df6b7052' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/functions_include.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SendinBlue\\Client\\' => 18,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SendinBlue\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'GuzzleHttp\\Client' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Client.php',
        'GuzzleHttp\\ClientInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/ClientInterface.php',
        'GuzzleHttp\\ClientTrait' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/ClientTrait.php',
        'GuzzleHttp\\Cookie\\CookieJar' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/CookieJar.php',
        'GuzzleHttp\\Cookie\\CookieJarInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/CookieJarInterface.php',
        'GuzzleHttp\\Cookie\\FileCookieJar' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/FileCookieJar.php',
        'GuzzleHttp\\Cookie\\SessionCookieJar' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/SessionCookieJar.php',
        'GuzzleHttp\\Cookie\\SetCookie' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Cookie/SetCookie.php',
        'GuzzleHttp\\Exception\\BadResponseException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/BadResponseException.php',
        'GuzzleHttp\\Exception\\ClientException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/ClientException.php',
        'GuzzleHttp\\Exception\\ConnectException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/ConnectException.php',
        'GuzzleHttp\\Exception\\GuzzleException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/GuzzleException.php',
        'GuzzleHttp\\Exception\\InvalidArgumentException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/InvalidArgumentException.php',
        'GuzzleHttp\\Exception\\RequestException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/RequestException.php',
        'GuzzleHttp\\Exception\\ServerException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/ServerException.php',
        'GuzzleHttp\\Exception\\TooManyRedirectsException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/TooManyRedirectsException.php',
        'GuzzleHttp\\Exception\\TransferException' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Exception/TransferException.php',
        'GuzzleHttp\\HandlerStack' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/HandlerStack.php',
        'GuzzleHttp\\Handler\\CurlFactory' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlFactory.php',
        'GuzzleHttp\\Handler\\CurlFactoryInterface' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlFactoryInterface.php',
        'GuzzleHttp\\Handler\\CurlHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlHandler.php',
        'GuzzleHttp\\Handler\\CurlMultiHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/CurlMultiHandler.php',
        'GuzzleHttp\\Handler\\EasyHandle' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/EasyHandle.php',
        'GuzzleHttp\\Handler\\MockHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/MockHandler.php',
        'GuzzleHttp\\Handler\\Proxy' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/Proxy.php',
        'GuzzleHttp\\Handler\\StreamHandler' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Handler/StreamHandler.php',
        'GuzzleHttp\\MessageFormatter' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/MessageFormatter.php',
        'GuzzleHttp\\Middleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Middleware.php',
        'GuzzleHttp\\Pool' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Pool.php',
        'GuzzleHttp\\PrepareBodyMiddleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/PrepareBodyMiddleware.php',
        'GuzzleHttp\\Promise\\AggregateException' => __DIR__ . '/..' . '/guzzlehttp/promises/src/AggregateException.php',
        'GuzzleHttp\\Promise\\CancellationException' => __DIR__ . '/..' . '/guzzlehttp/promises/src/CancellationException.php',
        'GuzzleHttp\\Promise\\Coroutine' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Coroutine.php',
        'GuzzleHttp\\Promise\\Create' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Create.php',
        'GuzzleHttp\\Promise\\Each' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Each.php',
        'GuzzleHttp\\Promise\\EachPromise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/EachPromise.php',
        'GuzzleHttp\\Promise\\FulfilledPromise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/FulfilledPromise.php',
        'GuzzleHttp\\Promise\\Is' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Is.php',
        'GuzzleHttp\\Promise\\Promise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Promise.php',
        'GuzzleHttp\\Promise\\PromiseInterface' => __DIR__ . '/..' . '/guzzlehttp/promises/src/PromiseInterface.php',
        'GuzzleHttp\\Promise\\PromisorInterface' => __DIR__ . '/..' . '/guzzlehttp/promises/src/PromisorInterface.php',
        'GuzzleHttp\\Promise\\RejectedPromise' => __DIR__ . '/..' . '/guzzlehttp/promises/src/RejectedPromise.php',
        'GuzzleHttp\\Promise\\RejectionException' => __DIR__ . '/..' . '/guzzlehttp/promises/src/RejectionException.php',
        'GuzzleHttp\\Promise\\TaskQueue' => __DIR__ . '/..' . '/guzzlehttp/promises/src/TaskQueue.php',
        'GuzzleHttp\\Promise\\TaskQueueInterface' => __DIR__ . '/..' . '/guzzlehttp/promises/src/TaskQueueInterface.php',
        'GuzzleHttp\\Promise\\Utils' => __DIR__ . '/..' . '/guzzlehttp/promises/src/Utils.php',
        'GuzzleHttp\\Psr7\\AppendStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/AppendStream.php',
        'GuzzleHttp\\Psr7\\BufferStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/BufferStream.php',
        'GuzzleHttp\\Psr7\\CachingStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/CachingStream.php',
        'GuzzleHttp\\Psr7\\DroppingStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/DroppingStream.php',
        'GuzzleHttp\\Psr7\\FnStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/FnStream.php',
        'GuzzleHttp\\Psr7\\Header' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Header.php',
        'GuzzleHttp\\Psr7\\InflateStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/InflateStream.php',
        'GuzzleHttp\\Psr7\\LazyOpenStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/LazyOpenStream.php',
        'GuzzleHttp\\Psr7\\LimitStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/LimitStream.php',
        'GuzzleHttp\\Psr7\\Message' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Message.php',
        'GuzzleHttp\\Psr7\\MessageTrait' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/MessageTrait.php',
        'GuzzleHttp\\Psr7\\MimeType' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/MimeType.php',
        'GuzzleHttp\\Psr7\\MultipartStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/MultipartStream.php',
        'GuzzleHttp\\Psr7\\NoSeekStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/NoSeekStream.php',
        'GuzzleHttp\\Psr7\\PumpStream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/PumpStream.php',
        'GuzzleHttp\\Psr7\\Query' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Query.php',
        'GuzzleHttp\\Psr7\\Request' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Request.php',
        'GuzzleHttp\\Psr7\\Response' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Response.php',
        'GuzzleHttp\\Psr7\\Rfc7230' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Rfc7230.php',
        'GuzzleHttp\\Psr7\\ServerRequest' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/ServerRequest.php',
        'GuzzleHttp\\Psr7\\Stream' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Stream.php',
        'GuzzleHttp\\Psr7\\StreamDecoratorTrait' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/StreamDecoratorTrait.php',
        'GuzzleHttp\\Psr7\\StreamWrapper' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/StreamWrapper.php',
        'GuzzleHttp\\Psr7\\UploadedFile' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UploadedFile.php',
        'GuzzleHttp\\Psr7\\Uri' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Uri.php',
        'GuzzleHttp\\Psr7\\UriNormalizer' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UriNormalizer.php',
        'GuzzleHttp\\Psr7\\UriResolver' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/UriResolver.php',
        'GuzzleHttp\\Psr7\\Utils' => __DIR__ . '/..' . '/guzzlehttp/psr7/src/Utils.php',
        'GuzzleHttp\\RedirectMiddleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/RedirectMiddleware.php',
        'GuzzleHttp\\RequestOptions' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/RequestOptions.php',
        'GuzzleHttp\\RetryMiddleware' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/RetryMiddleware.php',
        'GuzzleHttp\\TransferStats' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/TransferStats.php',
        'GuzzleHttp\\Utils' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/Utils.php',
        'Psr\\Http\\Client\\ClientExceptionInterface' => __DIR__ . '/..' . '/psr/http-client/src/ClientExceptionInterface.php',
        'Psr\\Http\\Client\\ClientInterface' => __DIR__ . '/..' . '/psr/http-client/src/ClientInterface.php',
        'Psr\\Http\\Client\\NetworkExceptionInterface' => __DIR__ . '/..' . '/psr/http-client/src/NetworkExceptionInterface.php',
        'Psr\\Http\\Client\\RequestExceptionInterface' => __DIR__ . '/..' . '/psr/http-client/src/RequestExceptionInterface.php',
        'Psr\\Http\\Message\\MessageInterface' => __DIR__ . '/..' . '/psr/http-message/src/MessageInterface.php',
        'Psr\\Http\\Message\\RequestInterface' => __DIR__ . '/..' . '/psr/http-message/src/RequestInterface.php',
        'Psr\\Http\\Message\\ResponseInterface' => __DIR__ . '/..' . '/psr/http-message/src/ResponseInterface.php',
        'Psr\\Http\\Message\\ServerRequestInterface' => __DIR__ . '/..' . '/psr/http-message/src/ServerRequestInterface.php',
        'Psr\\Http\\Message\\StreamInterface' => __DIR__ . '/..' . '/psr/http-message/src/StreamInterface.php',
        'Psr\\Http\\Message\\UploadedFileInterface' => __DIR__ . '/..' . '/psr/http-message/src/UploadedFileInterface.php',
        'Psr\\Http\\Message\\UriInterface' => __DIR__ . '/..' . '/psr/http-message/src/UriInterface.php',
        'SendinBlue\\Client\\ApiException' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/ApiException.php',
        'SendinBlue\\Client\\Api\\AccountApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/AccountApi.php',
        'SendinBlue\\Client\\Api\\AttributesApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/AttributesApi.php',
        'SendinBlue\\Client\\Api\\ContactsApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/ContactsApi.php',
        'SendinBlue\\Client\\Api\\EmailCampaignsApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/EmailCampaignsApi.php',
        'SendinBlue\\Client\\Api\\FoldersApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/FoldersApi.php',
        'SendinBlue\\Client\\Api\\ListsApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/ListsApi.php',
        'SendinBlue\\Client\\Api\\ProcessApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/ProcessApi.php',
        'SendinBlue\\Client\\Api\\ResellerApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/ResellerApi.php',
        'SendinBlue\\Client\\Api\\SMSCampaignsApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/SMSCampaignsApi.php',
        'SendinBlue\\Client\\Api\\SendersApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/SendersApi.php',
        'SendinBlue\\Client\\Api\\TransactionalEmailsApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/TransactionalEmailsApi.php',
        'SendinBlue\\Client\\Api\\TransactionalSMSApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/TransactionalSMSApi.php',
        'SendinBlue\\Client\\Api\\WebhooksApi' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Api/WebhooksApi.php',
        'SendinBlue\\Client\\Configuration' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Configuration.php',
        'SendinBlue\\Client\\HeaderSelector' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/HeaderSelector.php',
        'SendinBlue\\Client\\Model\\AbTestCampaignResult' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/AbTestCampaignResult.php',
        'SendinBlue\\Client\\Model\\AbTestCampaignResultClickedLinks' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/AbTestCampaignResultClickedLinks.php',
        'SendinBlue\\Client\\Model\\AbTestCampaignResultStatistics' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/AbTestCampaignResultStatistics.php',
        'SendinBlue\\Client\\Model\\AbTestVersionClicks' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/AbTestVersionClicks.php',
        'SendinBlue\\Client\\Model\\AbTestVersionClicksInner' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/AbTestVersionClicksInner.php',
        'SendinBlue\\Client\\Model\\AbTestVersionStats' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/AbTestVersionStats.php',
        'SendinBlue\\Client\\Model\\AddChildDomain' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/AddChildDomain.php',
        'SendinBlue\\Client\\Model\\AddContactToList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/AddContactToList.php',
        'SendinBlue\\Client\\Model\\AddCredits' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/AddCredits.php',
        'SendinBlue\\Client\\Model\\BlockDomain' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/BlockDomain.php',
        'SendinBlue\\Client\\Model\\CreateAttribute' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateAttribute.php',
        'SendinBlue\\Client\\Model\\CreateAttributeEnumeration' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateAttributeEnumeration.php',
        'SendinBlue\\Client\\Model\\CreateChild' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateChild.php',
        'SendinBlue\\Client\\Model\\CreateContact' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateContact.php',
        'SendinBlue\\Client\\Model\\CreateDoiContact' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateDoiContact.php',
        'SendinBlue\\Client\\Model\\CreateEmailCampaign' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateEmailCampaign.php',
        'SendinBlue\\Client\\Model\\CreateEmailCampaignRecipients' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateEmailCampaignRecipients.php',
        'SendinBlue\\Client\\Model\\CreateEmailCampaignSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateEmailCampaignSender.php',
        'SendinBlue\\Client\\Model\\CreateList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateList.php',
        'SendinBlue\\Client\\Model\\CreateModel' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateModel.php',
        'SendinBlue\\Client\\Model\\CreateReseller' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateReseller.php',
        'SendinBlue\\Client\\Model\\CreateSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateSender.php',
        'SendinBlue\\Client\\Model\\CreateSenderIps' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateSenderIps.php',
        'SendinBlue\\Client\\Model\\CreateSenderModel' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateSenderModel.php',
        'SendinBlue\\Client\\Model\\CreateSmsCampaign' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateSmsCampaign.php',
        'SendinBlue\\Client\\Model\\CreateSmsCampaignRecipients' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateSmsCampaignRecipients.php',
        'SendinBlue\\Client\\Model\\CreateSmtpEmail' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateSmtpEmail.php',
        'SendinBlue\\Client\\Model\\CreateSmtpTemplate' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateSmtpTemplate.php',
        'SendinBlue\\Client\\Model\\CreateSmtpTemplateSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateSmtpTemplateSender.php',
        'SendinBlue\\Client\\Model\\CreateUpdateContactModel' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateUpdateContactModel.php',
        'SendinBlue\\Client\\Model\\CreateUpdateFolder' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateUpdateFolder.php',
        'SendinBlue\\Client\\Model\\CreateWebhook' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreateWebhook.php',
        'SendinBlue\\Client\\Model\\CreatedProcessId' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/CreatedProcessId.php',
        'SendinBlue\\Client\\Model\\DeleteHardbounces' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/DeleteHardbounces.php',
        'SendinBlue\\Client\\Model\\EmailExportRecipients' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/EmailExportRecipients.php',
        'SendinBlue\\Client\\Model\\ErrorModel' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/ErrorModel.php',
        'SendinBlue\\Client\\Model\\GetAccount' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetAccount.php',
        'SendinBlue\\Client\\Model\\GetAccountMarketingAutomation' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetAccountMarketingAutomation.php',
        'SendinBlue\\Client\\Model\\GetAccountPlan' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetAccountPlan.php',
        'SendinBlue\\Client\\Model\\GetAccountRelay' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetAccountRelay.php',
        'SendinBlue\\Client\\Model\\GetAccountRelayData' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetAccountRelayData.php',
        'SendinBlue\\Client\\Model\\GetAggregatedReport' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetAggregatedReport.php',
        'SendinBlue\\Client\\Model\\GetAttributes' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetAttributes.php',
        'SendinBlue\\Client\\Model\\GetAttributesAttributes' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetAttributesAttributes.php',
        'SendinBlue\\Client\\Model\\GetAttributesEnumeration' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetAttributesEnumeration.php',
        'SendinBlue\\Client\\Model\\GetBlockedDomains' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetBlockedDomains.php',
        'SendinBlue\\Client\\Model\\GetCampaignOverview' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetCampaignOverview.php',
        'SendinBlue\\Client\\Model\\GetCampaignRecipients' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetCampaignRecipients.php',
        'SendinBlue\\Client\\Model\\GetCampaignStats' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetCampaignStats.php',
        'SendinBlue\\Client\\Model\\GetChildAccountCreationStatus' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildAccountCreationStatus.php',
        'SendinBlue\\Client\\Model\\GetChildDomain' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildDomain.php',
        'SendinBlue\\Client\\Model\\GetChildDomains' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildDomains.php',
        'SendinBlue\\Client\\Model\\GetChildInfo' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildInfo.php',
        'SendinBlue\\Client\\Model\\GetChildInfoApiKeys' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildInfoApiKeys.php',
        'SendinBlue\\Client\\Model\\GetChildInfoApiKeysV2' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildInfoApiKeysV2.php',
        'SendinBlue\\Client\\Model\\GetChildInfoApiKeysV3' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildInfoApiKeysV3.php',
        'SendinBlue\\Client\\Model\\GetChildInfoCredits' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildInfoCredits.php',
        'SendinBlue\\Client\\Model\\GetChildInfoStatistics' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildInfoStatistics.php',
        'SendinBlue\\Client\\Model\\GetChildrenList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetChildrenList.php',
        'SendinBlue\\Client\\Model\\GetClient' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetClient.php',
        'SendinBlue\\Client\\Model\\GetContactCampaignStats' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetContactCampaignStats.php',
        'SendinBlue\\Client\\Model\\GetContactCampaignStatsClicked' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetContactCampaignStatsClicked.php',
        'SendinBlue\\Client\\Model\\GetContactCampaignStatsOpened' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetContactCampaignStatsOpened.php',
        'SendinBlue\\Client\\Model\\GetContactCampaignStatsTransacAttributes' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetContactCampaignStatsTransacAttributes.php',
        'SendinBlue\\Client\\Model\\GetContactCampaignStatsUnsubscriptions' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetContactCampaignStatsUnsubscriptions.php',
        'SendinBlue\\Client\\Model\\GetContactDetails' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetContactDetails.php',
        'SendinBlue\\Client\\Model\\GetContacts' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetContacts.php',
        'SendinBlue\\Client\\Model\\GetDeviceBrowserStats' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetDeviceBrowserStats.php',
        'SendinBlue\\Client\\Model\\GetEmailCampaign' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetEmailCampaign.php',
        'SendinBlue\\Client\\Model\\GetEmailCampaigns' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetEmailCampaigns.php',
        'SendinBlue\\Client\\Model\\GetEmailEventReport' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetEmailEventReport.php',
        'SendinBlue\\Client\\Model\\GetEmailEventReportEvents' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetEmailEventReportEvents.php',
        'SendinBlue\\Client\\Model\\GetExtendedCampaignOverview' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedCampaignOverview.php',
        'SendinBlue\\Client\\Model\\GetExtendedCampaignOverviewSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedCampaignOverviewSender.php',
        'SendinBlue\\Client\\Model\\GetExtendedCampaignStats' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedCampaignStats.php',
        'SendinBlue\\Client\\Model\\GetExtendedClient' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedClient.php',
        'SendinBlue\\Client\\Model\\GetExtendedClientAddress' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedClientAddress.php',
        'SendinBlue\\Client\\Model\\GetExtendedContactDetails' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedContactDetails.php',
        'SendinBlue\\Client\\Model\\GetExtendedContactDetailsStatistics' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedContactDetailsStatistics.php',
        'SendinBlue\\Client\\Model\\GetExtendedContactDetailsStatisticsClicked' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedContactDetailsStatisticsClicked.php',
        'SendinBlue\\Client\\Model\\GetExtendedContactDetailsStatisticsLinks' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedContactDetailsStatisticsLinks.php',
        'SendinBlue\\Client\\Model\\GetExtendedContactDetailsStatisticsMessagesSent' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedContactDetailsStatisticsMessagesSent.php',
        'SendinBlue\\Client\\Model\\GetExtendedContactDetailsStatisticsOpened' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedContactDetailsStatisticsOpened.php',
        'SendinBlue\\Client\\Model\\GetExtendedContactDetailsStatisticsUnsubscriptions' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedContactDetailsStatisticsUnsubscriptions.php',
        'SendinBlue\\Client\\Model\\GetExtendedContactDetailsStatisticsUnsubscriptionsAdminUnsubscription' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedContactDetailsStatisticsUnsubscriptionsAdminUnsubscription.php',
        'SendinBlue\\Client\\Model\\GetExtendedContactDetailsStatisticsUnsubscriptionsUserUnsubscription' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedContactDetailsStatisticsUnsubscriptionsUserUnsubscription.php',
        'SendinBlue\\Client\\Model\\GetExtendedList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedList.php',
        'SendinBlue\\Client\\Model\\GetExtendedListCampaignStats' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetExtendedListCampaignStats.php',
        'SendinBlue\\Client\\Model\\GetFolder' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetFolder.php',
        'SendinBlue\\Client\\Model\\GetFolderLists' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetFolderLists.php',
        'SendinBlue\\Client\\Model\\GetFolders' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetFolders.php',
        'SendinBlue\\Client\\Model\\GetIp' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetIp.php',
        'SendinBlue\\Client\\Model\\GetIpFromSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetIpFromSender.php',
        'SendinBlue\\Client\\Model\\GetIps' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetIps.php',
        'SendinBlue\\Client\\Model\\GetIpsFromSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetIpsFromSender.php',
        'SendinBlue\\Client\\Model\\GetList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetList.php',
        'SendinBlue\\Client\\Model\\GetLists' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetLists.php',
        'SendinBlue\\Client\\Model\\GetProcess' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetProcess.php',
        'SendinBlue\\Client\\Model\\GetProcesses' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetProcesses.php',
        'SendinBlue\\Client\\Model\\GetReports' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetReports.php',
        'SendinBlue\\Client\\Model\\GetReportsReports' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetReportsReports.php',
        'SendinBlue\\Client\\Model\\GetSendersList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSendersList.php',
        'SendinBlue\\Client\\Model\\GetSendersListIps' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSendersListIps.php',
        'SendinBlue\\Client\\Model\\GetSendersListSenders' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSendersListSenders.php',
        'SendinBlue\\Client\\Model\\GetSharedTemplateUrl' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSharedTemplateUrl.php',
        'SendinBlue\\Client\\Model\\GetSmsCampaign' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSmsCampaign.php',
        'SendinBlue\\Client\\Model\\GetSmsCampaignOverview' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSmsCampaignOverview.php',
        'SendinBlue\\Client\\Model\\GetSmsCampaignStats' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSmsCampaignStats.php',
        'SendinBlue\\Client\\Model\\GetSmsCampaigns' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSmsCampaigns.php',
        'SendinBlue\\Client\\Model\\GetSmsEventReport' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSmsEventReport.php',
        'SendinBlue\\Client\\Model\\GetSmsEventReportEvents' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSmsEventReportEvents.php',
        'SendinBlue\\Client\\Model\\GetSmtpTemplateOverview' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSmtpTemplateOverview.php',
        'SendinBlue\\Client\\Model\\GetSmtpTemplateOverviewSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSmtpTemplateOverviewSender.php',
        'SendinBlue\\Client\\Model\\GetSmtpTemplates' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSmtpTemplates.php',
        'SendinBlue\\Client\\Model\\GetSsoToken' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetSsoToken.php',
        'SendinBlue\\Client\\Model\\GetStatsByBrowser' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetStatsByBrowser.php',
        'SendinBlue\\Client\\Model\\GetStatsByDevice' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetStatsByDevice.php',
        'SendinBlue\\Client\\Model\\GetStatsByDomain' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetStatsByDomain.php',
        'SendinBlue\\Client\\Model\\GetTransacAggregatedSmsReport' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacAggregatedSmsReport.php',
        'SendinBlue\\Client\\Model\\GetTransacBlockedContacts' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacBlockedContacts.php',
        'SendinBlue\\Client\\Model\\GetTransacBlockedContactsContacts' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacBlockedContactsContacts.php',
        'SendinBlue\\Client\\Model\\GetTransacBlockedContactsReason' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacBlockedContactsReason.php',
        'SendinBlue\\Client\\Model\\GetTransacEmailContent' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacEmailContent.php',
        'SendinBlue\\Client\\Model\\GetTransacEmailContentEvents' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacEmailContentEvents.php',
        'SendinBlue\\Client\\Model\\GetTransacEmailsList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacEmailsList.php',
        'SendinBlue\\Client\\Model\\GetTransacEmailsListTransactionalEmails' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacEmailsListTransactionalEmails.php',
        'SendinBlue\\Client\\Model\\GetTransacSmsReport' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacSmsReport.php',
        'SendinBlue\\Client\\Model\\GetTransacSmsReportReports' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetTransacSmsReportReports.php',
        'SendinBlue\\Client\\Model\\GetWebhook' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetWebhook.php',
        'SendinBlue\\Client\\Model\\GetWebhooks' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/GetWebhooks.php',
        'SendinBlue\\Client\\Model\\ManageIp' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/ManageIp.php',
        'SendinBlue\\Client\\Model\\ModelInterface' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/ModelInterface.php',
        'SendinBlue\\Client\\Model\\PostContactInfo' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/PostContactInfo.php',
        'SendinBlue\\Client\\Model\\PostContactInfoContacts' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/PostContactInfoContacts.php',
        'SendinBlue\\Client\\Model\\PostSendFailed' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/PostSendFailed.php',
        'SendinBlue\\Client\\Model\\PostSendSmsTestFailed' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/PostSendSmsTestFailed.php',
        'SendinBlue\\Client\\Model\\RemainingCreditModel' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RemainingCreditModel.php',
        'SendinBlue\\Client\\Model\\RemainingCreditModelChild' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RemainingCreditModelChild.php',
        'SendinBlue\\Client\\Model\\RemainingCreditModelReseller' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RemainingCreditModelReseller.php',
        'SendinBlue\\Client\\Model\\RemoveContactFromList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RemoveContactFromList.php',
        'SendinBlue\\Client\\Model\\RemoveCredits' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RemoveCredits.php',
        'SendinBlue\\Client\\Model\\RequestContactExport' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RequestContactExport.php',
        'SendinBlue\\Client\\Model\\RequestContactExportCustomContactFilter' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RequestContactExportCustomContactFilter.php',
        'SendinBlue\\Client\\Model\\RequestContactImport' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RequestContactImport.php',
        'SendinBlue\\Client\\Model\\RequestContactImportNewList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RequestContactImportNewList.php',
        'SendinBlue\\Client\\Model\\RequestSmsRecipientExport' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/RequestSmsRecipientExport.php',
        'SendinBlue\\Client\\Model\\SendReport' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendReport.php',
        'SendinBlue\\Client\\Model\\SendReportEmail' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendReportEmail.php',
        'SendinBlue\\Client\\Model\\SendSms' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSms.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmail' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmail.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmailAttachment' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmailAttachment.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmailBcc' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmailBcc.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmailCc' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmailCc.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmailMessageVersions' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmailMessageVersions.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmailReplyTo' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmailReplyTo.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmailReplyTo1' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmailReplyTo1.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmailSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmailSender.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmailTo' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmailTo.php',
        'SendinBlue\\Client\\Model\\SendSmtpEmailTo1' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendSmtpEmailTo1.php',
        'SendinBlue\\Client\\Model\\SendTestEmail' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendTestEmail.php',
        'SendinBlue\\Client\\Model\\SendTestSms' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendTestSms.php',
        'SendinBlue\\Client\\Model\\SendTransacSms' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/SendTransacSms.php',
        'SendinBlue\\Client\\Model\\UpdateAttribute' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateAttribute.php',
        'SendinBlue\\Client\\Model\\UpdateAttributeEnumeration' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateAttributeEnumeration.php',
        'SendinBlue\\Client\\Model\\UpdateCampaignStatus' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateCampaignStatus.php',
        'SendinBlue\\Client\\Model\\UpdateChild' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateChild.php',
        'SendinBlue\\Client\\Model\\UpdateChildAccountStatus' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateChildAccountStatus.php',
        'SendinBlue\\Client\\Model\\UpdateChildDomain' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateChildDomain.php',
        'SendinBlue\\Client\\Model\\UpdateContact' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateContact.php',
        'SendinBlue\\Client\\Model\\UpdateEmailCampaign' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateEmailCampaign.php',
        'SendinBlue\\Client\\Model\\UpdateEmailCampaignRecipients' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateEmailCampaignRecipients.php',
        'SendinBlue\\Client\\Model\\UpdateEmailCampaignSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateEmailCampaignSender.php',
        'SendinBlue\\Client\\Model\\UpdateList' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateList.php',
        'SendinBlue\\Client\\Model\\UpdateSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateSender.php',
        'SendinBlue\\Client\\Model\\UpdateSmsCampaign' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateSmsCampaign.php',
        'SendinBlue\\Client\\Model\\UpdateSmtpTemplate' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateSmtpTemplate.php',
        'SendinBlue\\Client\\Model\\UpdateSmtpTemplateSender' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateSmtpTemplateSender.php',
        'SendinBlue\\Client\\Model\\UpdateWebhook' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UpdateWebhook.php',
        'SendinBlue\\Client\\Model\\UploadImageToGallery' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/Model/UploadImageToGallery.php',
        'SendinBlue\\Client\\ObjectSerializer' => __DIR__ . '/..' . '/sendinblue/api-v3-sdk/lib/ObjectSerializer.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit411781ce819b3aacaf65c92040d2f340::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit411781ce819b3aacaf65c92040d2f340::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit411781ce819b3aacaf65c92040d2f340::$classMap;

        }, null, ClassLoader::class);
    }
}
