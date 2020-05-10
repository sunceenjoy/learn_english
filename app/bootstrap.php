<?php

define('DOCROOT', dirname(__DIR__));

$c = new \Eng\Core\Container();
$c['res_dir'] = DOCROOT.'/res';
$c['config_dir'] = DOCROOT.'/config';
$c['app_dir'] = DOCROOT.'/app';
$c['log_dir'] = $c['res_dir'].'/logs';
$c['voice_save_path'] = DOCROOT.'/webroot/voice';

$c['session'] = function () {
    $params = [
        'gc_maxlifetime' => 3600 * 24 * 10000,
        'cookie_lifetime' => 3600 * 24 * 10000
    ];
    return new Symfony\Component\HttpFoundation\Session\Session(new Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage($params));
};

$c['env'] = function ($c) {
    if ($c['session']->get('isTester')) {
        return new \Eng\Core\Environment('eng_dev');
    }
    return new \Eng\Core\Environment(getenv('ENG_ENV'));
};

$c['config'] = function ($c) {
    $appIni = parse_ini_file($c['config_dir'].'/default/app.ini', true);
    $envIni = parse_ini_file($c['config_dir'].'/'.($c['env']->isProd() ? 'prod' : 'dev').'/app.ini', true);
    $configArray = array_merge($appIni['default'], $envIni['default']);
    return new \Eng\Core\Config($configArray);
};

$c['log.main'] = function ($c) {
    $logger = new \Monolog\Logger('main');
    $level  = $c['config']['debug'] ? \Monolog\Logger::DEBUG : \Monolog\Logger::INFO;
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($c['log_dir'].'/main.log', $level));

    $level  = $c['config']['debug'] ? \Monolog\Logger::DEBUG : \Monolog\Logger::ERROR; // Will mail if error leve is above this setting.
    $logger->pushHandler(new \Monolog\Handler\SwiftMailerHandler($c['mailer'], new \Swift_Message('Module error need to repair'), $level));
    return $logger;
};

$c['mailer'] = function ($c) {
    // Using the native php mail function
    $transport = \Swift_MailTransport::newInstance();

    if ($c['config']['debug']) {
        // Debug mode will write all sent emails to cache dir
        $transport = \Swift_SpoolTransport::newInstance(
            new \Swift_FileSpool($c['res_dir'].'/cache/mailspool')
        );
    }

    return \Swift_Mailer::newInstance($transport);
};

$c['router.routes'] = function ($c) {
    $routes = require $c['app_dir'].'/routing.php';
    return $routes;
};

$c['router'] = function ($c) {
    if (isset($c['request'])) {
        // If the request is set, use it
        // http://symfony.com/doc/current/components/routing/introduction.html#components-routing-http-foundation
        $context = new Symfony\Component\Routing\RequestContext();
        $context->fromRequest($c['request']);
    } else {
        $context = new Symfony\Component\Routing\RequestContext(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
    }

    $matcher = new Symfony\Component\Routing\Matcher\UrlMatcher($c['router.routes'], $context);

    return $matcher;
};

$c['dispather'] = function ($c) {
     return new \Symfony\Component\EventDispatcher\EventDispatcher();
};

$c['request'] = function ($c) {
    return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
};

$c['util.uri'] = function ($c) {
    return new \Eng\Core\Util\Uri($c);
};

$c['twig'] = function ($c) {
    $loader = new \Twig_Loader_Filesystem($c['res_dir'].'/templates');
    // Use cache when we are not in cli and debug is off
    $useCache = (PHP_SAPI !== 'cli' && !$c['config']['debug']);
    $twig = new \Twig_Environment(
        $loader,
        array(
            'cache' => ($useCache) ? $c['res_dir'].'/cache/twig' : false,
            'debug' => $c['config']['debug'],
        )
    );

    if ($c['config']['debug']) {
        // Add Twig's debug extension if debug is on
        $twig->addExtension(new \Twig_Extension_Debug());
    }

    $twig->addExtension(new \Eng\Core\Twig\TransExtension($c['translator']));
    //$twig->addExtension(new \ESP\Core\Twig\TextFilterExtension());
    $twig->addGlobal('config', $c['config']); // Global variables in twig:  {{ config.attribute }}
    $twig->addGlobal('env', $c['env']);
    $twig->addGlobal('uri', $c['util.uri']);
    $twig->addGlobal('session', $c['session']);
    $twig->addGlobal('request', $c['request']);
    return $twig;
};

$c['db.eng'] = function ($c) {
    $config = new \Doctrine\DBAL\Configuration();
    $connectionParams = array(
        //'wrapperClass' => 'ESP\Core\DBAL\Connection',
        'dbname'       => $c['config']['db.eng.db'],
        'user'         => $c['config']['db.eng.user'],
        'password'     => $c['config']['db.eng.pass'],
        'host'         => $c['config']['db.eng.host'],
        'port'         => $c['config']['db.eng.port'],
        'driver'       => 'pdo_mysql',
        'charset'      => 'utf8',
    );

    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    return $conn;
};

$c['doctrine.entity_manager'] = function ($c) {
    $isDevMode = true;
    $config = \Doctrine\ORM\Tools\Setup::createConfiguration($isDevMode); // This can also set cache or other things
    $config->addEntityNamespace('Eng', 'Eng\\Core\\Repository\\Entity');
    $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(new \Doctrine\Common\Annotations\AnnotationReader(), $c['res_dir'].'/logs');
    \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
    $config->setMetadataDriverImpl($driver);

    $entityManager = \Doctrine\ORM\EntityManager::create($c['db.eng'], $config);
    return $entityManager;
};

$c['translator'] = function ($c) {
    $translator = new \Symfony\Component\Translation\Translator('en');
    $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
    $translator->addResource('yaml', $c['res_dir'].'/lang/cn.yml', 'cn');
    $translator->addResource('yaml', $c['res_dir'].'/lang/jpa.yml', 'jap');
    $translator->setLocale('cn');
    return $translator;
};

$c['wordVoiceDownloader'] = function ($c) {
    $wordVoiceDownloader = new \Eng\Core\Module\Words\Voice\VoiceDownloader($c['log.main'], $c['voice_save_path'].'/words');
    $wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\Google());
    $wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\Merriam());
    $wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\YouDao());
    //$wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\JinShan());
    $wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\CamBridge());
    return $wordVoiceDownloader;
};

$c['wordMeanDownloader'] = function ($c) {
    $wordMeanDownloader = new \Eng\Core\Module\Words\Mean\MeanDownloader($c['log.main']);
    $wordMeanDownloader->addVendor(new \Eng\Core\Module\Words\Mean\Vendor\YouDao());
    $wordMeanDownloader->addVendor(new \Eng\Core\Module\Words\Mean\Vendor\JinShan());
    return $wordMeanDownloader;
};

$c['phraseVoiceDownloader'] = function ($c) {
    $phraseVoiceDownloader = new \Eng\Core\Module\Phrases\Voice\VoiceDownloader($c['log.main'], $c['voice_save_path'].'/phrases');
    $phraseVoiceDownloader->addVendor(new \Eng\Core\Module\Phrases\Voice\Vendor\GoogleTranslation(null, $c['config']['google_api_key']));
    $phraseVoiceDownloader->addVendor(new \Eng\Core\Module\Phrases\Voice\Vendor\NaturalReaders());
    $phraseVoiceDownloader->addVendor(new \Eng\Core\Module\Phrases\Voice\Vendor\JinShan());
    return $phraseVoiceDownloader;
};

$c['pronsPhraseVoiceDownloader'] = function ($c) {
    $phraseVoiceDownloader = new \Eng\Core\Module\Phrases\Voice\VoiceDownloader($c['log.main'], $c['voice_save_path'].'/prons');
    $phraseVoiceDownloader->addVendor(new \Eng\Core\Module\Phrases\Voice\Vendor\NaturalReaders());
    $phraseVoiceDownloader->addVendor(new \Eng\Core\Module\Phrases\Voice\Vendor\JinShan());
    return $phraseVoiceDownloader;
};

$c['pronsWordVoiceDownloader'] = function ($c) {
    $wordVoiceDownloader = new \Eng\Core\Module\Words\Voice\VoiceDownloader($c['log.main'], $c['voice_save_path'].'/prons');
    $wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\Google());
    $wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\Merriam());
    $wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\YouDao());
    //$wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\JinShan());
    $wordVoiceDownloader->addVendor(new \Eng\Core\Module\Words\Voice\Vendor\CamBridge());
    return $wordVoiceDownloader;
};

$c['entity.serializer'] = function ($c) {
    $encoders = array(new Symfony\Component\Serializer\Encoder\XmlEncoder(), new Symfony\Component\Serializer\Encoder\JsonEncoder());
    $normalizers = array(new Symfony\Component\Serializer\Normalizer\ObjectNormalizer());

    return $serializer = new Symfony\Component\Serializer\Serializer($normalizers, $encoders);
};

$c['dao_authentication_provider'] = function ($c) {
    $userProvider = new Eng\Core\Security\Provider\DaoUserProvider($c['doctrine.entity_manager']->getRepository('Eng:UserEntity'));
    $userChecker = new Symfony\Component\Security\Core\User\UserChecker();
    $providerKey = 'mmyyabb';
    $encoder = new Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder(15);
    $encoderFactory = new Symfony\Component\Security\Core\Encoder\EncoderFactory([\Symfony\Component\Security\Core\User\User::class => $encoder]);
    return new Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider($userProvider, $userChecker, $providerKey, $encoderFactory);
};

$c['auth'] = function ($c) {
    return new Eng\Core\Security\Auth($c['session'], $c['dao_authentication_provider']);
};

\Eng\Core\ErrorHandler::register($c['env'], $c['mailer'], $c['config']['debug']);

require DOCROOT.'/app/event.php';
