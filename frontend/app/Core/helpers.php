<?php

declare(strict_types=1);

use App\Core\Env;

function env(string $key, mixed $default = null): mixed
{
    return Env::get($key, $default);
}

function config(string $key, mixed $default = null): mixed
{
    return app()->config($key, $default);
}

function app(): App\Core\Application
{
    return App\Core\Application::getInstance();
}

function base_path(string $path = ''): string
{
    $base = dirname(__DIR__, 2);
    return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $base;
}

function storage_path(string $path = ''): string
{
    $base = base_path('storage');
    return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $base;
}

function normalize_path(string $path): string
{
    $path = parse_url($path, PHP_URL_PATH) ?: '/';
    $path = preg_replace('#/+#', '/', $path) ?: '/';

    if ($path !== '/' && str_ends_with($path, '.php')) {
        $path = substr($path, 0, -4);
    }

    $path = '/' . trim($path, '/');
    return $path === '//' ? '/' : $path;
}

function request_path(): string
{
    return normalize_path((string) ($_SERVER['REQUEST_URI'] ?? '/'));
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $baseUrl = rtrim((string) env('APP_URL', ''), '/');
    $path = '/' . ltrim($path, '/');
    if ($baseUrl !== '') {
        return $baseUrl . ($path === '/' ? '' : $path);
    }

    return $path === '/' ? '/' : $path;
}

function asset_url(string $path = ''): string
{
    return url($path);
}

function api_url(string $path = ''): string
{
    $normalized = normalize_path('/api/' . ltrim($path, '/'));
    return url($normalized);
}

function current_language(): string
{
    return $_SESSION['language'] ?? (string) env('APP_LOCALE', 'ru');
}

function available_languages(): array
{
    return ['ru', 'kk', 'en'];
}

function language_label(string $code): string
{
    return match ($code) {
        'ru' => 'Русский',
        'kk' => 'Қазақша',
        'en' => 'English',
        default => $code,
    };
}

function language_short(string $code): string
{
    return match ($code) {
        'ru' => 'RU',
        'kk' => 'KK',
        'en' => 'EN',
        default => $code,
    };
}

function translations(): array
{
    return [
        'ru' => [
            'home' => 'Главная',
            'diagnosis' => 'Диагностика',
            'results' => 'Результаты',
            'recommendations' => 'Рекомендации',
            'therapist' => 'Для логопеда',
            'courses' => 'Курсы',
            'dataset' => 'Датасет',
            'dataset_history' => 'История датасета',
            'lang_switch' => 'Қаз',
            'subtitle' => 'AI диагностика речевых нарушений',
            'footer' => '© 2026 TilDamu.kz. Профессиональная помощь в развитии речи детей.',
            'common' => [
                'login' => 'Войти',
                'logout' => 'Выйти',
                'register' => 'Зарегистрироваться',
                'email' => 'Email',
                'password' => 'Пароль',
                'phone' => 'Телефон',
                'full_name' => 'Полное имя',
                'save' => 'Сохранить',
                'send' => 'Отправить',
                'watch' => 'Смотреть',
                'download_pdf' => 'Скачать PDF',
                'child' => 'Ребёнок',
                'free_start' => 'Начать бесплатно',
                'notes_saved' => 'Сохранено',
                'priority_low' => 'Приоритет: Низкий',
                'priority_medium' => 'Приоритет: Средний',
                'priority_high' => 'Приоритет: Высокий',
                'avatar' => 'Аватар',
                'optional' => 'необязательно',
            ],
            'auth' => [
                'login_title' => 'Вход в TilDamu',
                'login_subtitle' => 'Войдите, чтобы продолжить',
                'register_title' => 'Регистрация',
                'register_subtitle' => 'Создайте аккаунт в TilDamu.kz',
                'no_account' => 'Нет аккаунта?',
                'has_account' => 'Уже есть аккаунт?',
                'role_title' => 'Я регистрируюсь как',
                'role_parent' => 'Родитель',
                'role_patient' => 'Клиент / родитель',
                'role_therapist' => 'Логопед',
                'patient_notice' => 'Регистрация открыта для клиентов. Администраторы, разработчики и логопеды создаются администратором.',
                'avatar_title' => 'Аватар профиля',
                'avatar_hint' => 'Поддерживаются JPG, JPEG и PNG до 5 МБ.',
                'avatar_change_hint' => 'Можно загрузить новый аватар при входе.',
                'child_name' => 'Имя ребёнка',
                'child_age' => 'Возраст ребёнка',
                'password_confirm' => 'Подтверждение пароля',
                'password_min' => 'Минимум 6 символов',
                'upload_avatar' => 'Загрузить аватар',
                'selected_avatar' => 'Выбранный аватар',
                'fill_all' => 'Заполните все поля.',
                'invalid_credentials' => 'Неверный email или пароль.',
                'enter_name' => 'Введите ваше имя.',
                'invalid_email' => 'Введите корректный email.',
                'password_length' => 'Пароль должен быть не менее 6 символов.',
                'password_mismatch' => 'Пароли не совпадают.',
                'enter_child_name' => 'Введите имя ребёнка.',
                'email_exists' => 'Пользователь с таким email уже зарегистрирован.',
                'register_error' => 'Ошибка при регистрации. Проверьте подключение к БД.',
                'avatar_type_error' => 'Разрешены только JPG, JPEG и PNG.',
                'avatar_size_error' => 'Размер аватара не должен превышать 5 МБ.',
                'avatar_upload_error' => 'Не удалось сохранить аватар. Проверьте права на папку storage/uploads/avatars.',
            ],
            'chat' => [
                'empty' => 'Сообщение пустое.',
                'placeholder' => 'Например: как помочь ребёнку со звуком Р дома?',
                'welcome' => 'Здравствуйте! Я могу объяснить результаты, подсказать домашние упражнения и помочь родителям понять следующий шаг.',
                'temporary_unavailable' => 'Сервис временно недоступен. Попробуйте чуть позже.',
                'failed_reply' => 'Не удалось получить ответ.',
                'title' => 'Чат-бот для родителей',
                'subtitle' => 'Задавайте вопросы о речи ребёнка и домашней практике.',
            ],
            'results_page' => [
                'ai_explanation' => 'Объяснение AI для родителей',
                'good_news' => 'Хорошая новость',
                'good_news_text' => 'При правильной коррекционной работе большинство детей полностью исправляют произношение в течение 3-6 месяцев.',
            ],
            'therapist_page' => [
                'ai_recommendations' => 'AI рекомендации для логопеда',
                'notes_saved' => 'Сохранено',
            ],
            'recommendations_page' => [
                'completed' => 'Готово',
                'bot_title' => 'Чат-бот для родителей',
            ],
            'diagnosis_page' => [
                'title' => 'AI Диагностика речи',
                'subtitle' => 'Повторяйте слова четко и спокойно',
                'progress' => 'Прогресс теста',
                'ready' => 'Готов к записи',
                'repeat_word' => 'Повторите слово',
                'hint_ready' => 'Нажмите на микрофон чтобы начать запись',
                'skip' => 'Пропустить слово',
                'tips_title' => 'Советы для точной диагностики',
                'tip1' => 'Находитесь в тихом помещении без посторонних звуков',
                'tip2' => 'Держите микрофон на расстоянии 15-20 см от рта ребенка',
                'tip3' => 'Говорите естественным темпом, не торопитесь',
                'tip4' => 'Если ребенок устал, сделайте перерыв и продолжите позже',
                'listen' => 'Прослушать',
            ],
            'courses_page' => [
                'title' => 'Курсы лечения',
                'subtitle' => 'Онлайн-курсы терапии с упражнениями и материалами',
                'create' => 'Создать курс',
                'no_courses' => 'Курсов пока нет',
                'no_courses_desc' => 'Создайте первый курс лечения.',
                'form_title' => 'Название курса',
                'form_desc' => 'Описание',
                'form_content' => 'Содержание курса',
                'form_media' => 'Медиа-файлы',
                'form_media_hint' => 'Загрузите изображения, аудио или видео (макс. 50 МБ каждый)',
                'form_submit' => 'Создать курс',
                'lessons' => 'уроков',
                'by' => 'от',
            ],
            'admin_users_page' => [
                'nav' => 'Пользователи',
                'eyebrow' => 'Доступы',
                'title' => 'Пользователи',
                'subtitle' => 'Администратор создаёт служебные аккаунты, разработчик может просматривать список.',
                'total' => 'Всего',
                'create_title' => 'Создать пользователя',
                'create_hint' => 'Клиенты могут регистрироваться сами, а служебные роли выдаёт администратор.',
                'role' => 'Роль',
                'create' => 'Создать',
                'table_title' => 'Список пользователей',
                'admin_hint' => 'Администратор может создавать, менять роли и отключать пользователей.',
                'readonly_hint' => 'Режим просмотра: управление доступно только администратору.',
                'empty_text' => 'Пользователей пока нет.',
                'status' => 'Статус',
                'last_login' => 'Последний вход',
                'actions' => 'Действия',
                'active' => 'Активен',
                'disabled' => 'Отключён',
                'new_password' => 'Новый пароль',
                'disable' => 'Отключить',
                'enable' => 'Включить',
                'created' => 'Пользователь создан.',
                'updated' => 'Пользователь обновлён.',
                'email_exists' => 'Пользователь с таким email уже существует.',
                'save_error' => 'Не удалось сохранить пользователя.',
                'invalid_role' => 'Недопустимая роль пользователя.',
                'not_found' => 'Пользователь не найден.',
                'self_disable_forbidden' => 'Нельзя отключить собственный аккаунт.',
            ],
            'dataset_page' => [
                'title' => 'Сбор датасета',
                'subtitle' => 'Помогите нам собрать данные для улучшения AI',
                'info_title' => 'Данные ребёнка',
                'child_name' => 'Имя ребёнка',
                'child_id' => 'ID ребёнка',
                'child_age' => 'Возраст',
                'gender' => 'Пол',
                'male' => 'Мужской',
                'female' => 'Женский',
                'ready' => 'Готов к записи',
                'repeat_word' => 'Повторите слово',
                'hint_ready' => 'Нажмите на микрофон чтобы начать запись',
                'skip' => 'Пропустить слово',
                'child_name_placeholder' => 'Например: Анар',
                'child_id_placeholder' => 'Например: 101',
                'child_age_placeholder' => 'Например: 6',
                'disorder_type' => 'Тип нарушения',
                'disorder_type_placeholder' => 'Выберите тип нарушения',
                'disorder_zrr' => 'ЗРР (Задержка речевого развития)',
                'disorder_dyslalia' => 'Дислалия',
                'disorder_onr' => 'ОНР (Общее недоразвитие речи)',
                'disorder_dysarthria' => 'Дизартрия',
                'disorder_stuttering' => 'Заикание',
                'recording_status' => 'Идет запись...',
                'recording_hint' => 'Говорите четко. Нажмите ещё раз чтобы остановить.',
                'analyzing_status' => 'AI анализирует...',
                'analyzing_hint' => 'Анализируем произношение...',
                'report_status' => 'Формируем отчёт...',
                'report_hint' => 'Собираем AI-диагностику...',
                'mic_error_https' => 'Страница должна открываться по HTTPS. Иначе браузер блокирует микрофон.',
                'mic_error_browser' => 'Браузер не поддерживает запись. Используйте Chrome, Edge, Firefox или Safari.',
                'mic_error_recorder' => 'MediaRecorder не поддерживается в этом браузере.',
                'mic_error_denied' => 'Доступ к микрофону запрещён. Разрешите микрофон в настройках браузера.',
                'mic_error_not_found' => 'Микрофон не найден. Подключите устройство и обновите страницу.',
                'mic_error_empty' => 'Запись получилась пустой. Попробуйте ещё раз.',
                'mic_error_generic' => 'Ошибка микрофона',
                'complete_error' => 'Ошибка завершения',
            ],
        ],
        'kk' => [
            'home' => 'Басты бет',
            'diagnosis' => 'Диагностика',
            'results' => 'Нәтижелер',
            'recommendations' => 'Ұсыныстар',
            'therapist' => 'Логопедке',
            'courses' => 'Курстар',
            'dataset' => 'Датасет',
            'dataset_history' => 'Датасет тарихы',
            'lang_switch' => 'Рус',
            'subtitle' => 'Сөйлеу бұзылыстарын AI диагностикасы',
            'footer' => '© 2026 TilDamu.kz. Балалардың сөйлеу дамуына кәсіби көмек.',
            'common' => [
                'login' => 'Кіру',
                'logout' => 'Шығу',
                'register' => 'Тіркелу',
                'email' => 'Email',
                'password' => 'Құпиясөз',
                'phone' => 'Телефон',
                'full_name' => 'Толық аты-жөні',
                'save' => 'Сақтау',
                'send' => 'Жіберу',
                'watch' => 'Көру',
                'download_pdf' => 'PDF жүктеу',
                'child' => 'Бала',
                'free_start' => 'Тегін бастау',
                'notes_saved' => 'Сақталды',
                'priority_low' => 'Басымдық: Төмен',
                'priority_medium' => 'Басымдық: Орташа',
                'priority_high' => 'Басымдық: Жоғары',
                'avatar' => 'Аватар',
                'optional' => 'міндетті емес',
            ],
            'auth' => [
                'login_title' => 'TilDamu жүйесіне кіру',
                'login_subtitle' => 'Жалғастыру үшін жүйеге кіріңіз',
                'register_title' => 'Тіркелу',
                'register_subtitle' => 'TilDamu.kz жүйесінде аккаунт ашыңыз',
                'no_account' => 'Аккаунтыңыз жоқ па?',
                'has_account' => 'Аккаунтыңыз бар ма?',
                'role_title' => 'Мен мына рөлмен тіркелемін',
                'role_parent' => 'Ата-ана',
                'role_patient' => 'Клиент / ата-ана',
                'role_therapist' => 'Логопед',
                'patient_notice' => 'Тіркелу клиенттерге ашық. Әкімшілерді, әзірлеушілерді және логопедтерді әкімші жасайды.',
                'avatar_title' => 'Профиль аватары',
                'avatar_hint' => 'JPG, JPEG және PNG форматтары, 5 МБ-қа дейін.',
                'avatar_change_hint' => 'Кіргенде жаңа аватар жүктеуге болады.',
                'child_name' => 'Баланың аты',
                'child_age' => 'Баланың жасы',
                'password_confirm' => 'Құпиясөзді растау',
                'password_min' => 'Кемінде 6 таңба',
                'upload_avatar' => 'Аватар жүктеу',
                'selected_avatar' => 'Таңдалған аватар',
                'fill_all' => 'Барлық өрістерді толтырыңыз.',
                'invalid_credentials' => 'Email немесе құпиясөз қате.',
                'enter_name' => 'Атыңызды енгізіңіз.',
                'invalid_email' => 'Дұрыс email енгізіңіз.',
                'password_length' => 'Құпиясөз кемінде 6 таңбадан тұруы керек.',
                'password_mismatch' => 'Құпиясөздер сәйкес келмейді.',
                'enter_child_name' => 'Баланың атын енгізіңіз.',
                'email_exists' => 'Мұндай email-пен пайдаланушы тіркелген.',
                'register_error' => 'Тіркелу кезінде қате шықты. ДҚ қосылымын тексеріңіз.',
                'avatar_type_error' => 'Тек JPG, JPEG және PNG файлдарына рұқсат етіледі.',
                'avatar_size_error' => 'Аватар көлемі 5 МБ-тан аспауы керек.',
                'avatar_upload_error' => 'Аватарды сақтау мүмкін болмады. storage/uploads/avatars қалтасының құқықтарын тексеріңіз.',
            ],
            'chat' => [
                'empty' => 'Хабарлама бос.',
                'placeholder' => 'Мысалы: балаға Р дыбысын үйде қалай жаттықтыруға болады?',
                'welcome' => 'Сәлеметсіз бе! Мен нәтижелерді түсіндіріп, үй жаттығуларын ұсынып, келесі қадамды таңдауға көмектесе аламын.',
                'temporary_unavailable' => 'Сервис уақытша қолжетімсіз. Кейінірек қайталап көріңіз.',
                'failed_reply' => 'Жауап алу мүмкін болмады.',
                'title' => 'Ата-аналарға арналған чат-бот',
                'subtitle' => 'Баланың сөйлеуі мен үйдегі жаттығулар туралы сұрақтар қойыңыз.',
            ],
            'results_page' => [
                'ai_explanation' => 'Ата-аналарға арналған AI түсіндірмесі',
                'good_news' => 'Жақсы жаңалық',
                'good_news_text' => 'Дұрыс түзету жұмысы жүргізілсе, балалардың көпшілігі 3-6 ай ішінде дыбыстарды толық түзетеді.',
            ],
            'therapist_page' => [
                'ai_recommendations' => 'Логопедке арналған AI ұсыныстары',
                'notes_saved' => 'Сақталды',
            ],
            'recommendations_page' => [
                'completed' => 'Дайын',
                'bot_title' => 'Ата-аналарға арналған чат-бот',
            ],
            'diagnosis_page' => [
                'title' => 'AI Сөйлеу диагностикасы',
                'subtitle' => 'Сөздерді анық және баяу қайталаңыз',
                'progress' => 'Тест барысы',
                'ready' => 'Жазуға дайын',
                'repeat_word' => 'Сөзді қайталаңыз',
                'hint_ready' => 'Жазуды бастау үшін микрофонды басыңыз',
                'skip' => 'Сөзді өткізіп жіберу',
                'tips_title' => 'Дәл диагностикаға арналған кеңестер',
                'tip1' => 'Бөгде дыбыстары жоқ тыныш бөлмеде болыңыз',
                'tip2' => 'Микрофонды баланың аузынан 15-20 см қашықтықта ұстаңыз',
                'tip3' => 'Табиғи қарқынмен сөйлеңіз, асықпаңыз',
                'tip4' => 'Бала шаршаса, үзіліс жасап, кейін жалғастырыңыз',
                'listen' => 'Тыңдау',
            ],
            'courses_page' => [
                'title' => 'Емдеу курстары',
                'subtitle' => 'Жаттығулар мен материалдары бар онлайн терапия курстары',
                'create' => 'Курс құру',
                'no_courses' => 'Курстар әлі жоқ',
                'no_courses_desc' => 'Алғашқы емдеу курсын жасаңыз.',
                'form_title' => 'Курс атауы',
                'form_desc' => 'Сипаттама',
                'form_content' => 'Курс мазмұны',
                'form_media' => 'Медиа файлдар',
                'form_media_hint' => 'Суреттер, аудио немесе видео жүктеңіз (әрқайсысы 50 МБ-ға дейін)',
                'form_submit' => 'Курс құру',
                'lessons' => 'сабақ',
                'by' => 'автор',
            ],
            'admin_users_page' => [
                'nav' => 'Пайдаланушылар',
                'eyebrow' => 'Қолжетімділік',
                'title' => 'Пайдаланушылар',
                'subtitle' => 'Әкімші қызметтік аккаунттарды жасайды, әзірлеуші тізімді қарай алады.',
                'total' => 'Барлығы',
                'create_title' => 'Пайдаланушы жасау',
                'create_hint' => 'Клиенттер өздері тіркеле алады, ал қызметтік рөлдерді әкімші береді.',
                'role' => 'Рөл',
                'create' => 'Жасау',
                'table_title' => 'Пайдаланушылар тізімі',
                'admin_hint' => 'Әкімші пайдаланушыларды жасап, рөлдерін өзгертіп, өшіре алады.',
                'readonly_hint' => 'Көру режимі: басқару тек әкімшіге қолжетімді.',
                'empty_text' => 'Әзірге пайдаланушылар жоқ.',
                'status' => 'Күйі',
                'last_login' => 'Соңғы кіру',
                'actions' => 'Әрекеттер',
                'active' => 'Белсенді',
                'disabled' => 'Өшірілген',
                'new_password' => 'Жаңа құпиясөз',
                'disable' => 'Өшіру',
                'enable' => 'Қосу',
                'created' => 'Пайдаланушы жасалды.',
                'updated' => 'Пайдаланушы жаңартылды.',
                'email_exists' => 'Бұл email арқылы пайдаланушы бар.',
                'save_error' => 'Пайдаланушыны сақтау мүмкін болмады.',
                'invalid_role' => 'Пайдаланушы рөлі дұрыс емес.',
                'not_found' => 'Пайдаланушы табылмады.',
                'self_disable_forbidden' => 'Өз аккаунтыңызды өшіруге болмайды.',
            ],
            'dataset_page' => [
                'title' => 'Датасет жинау',
                'subtitle' => 'AI жақсарту үшін деректер жинауға көмектесіңіз',
                'info_title' => 'Баланың деректері',
                'child_name' => 'Баланың есімі',
                'child_id' => 'Баланың ID-і',
                'child_age' => 'Жасы',
                'gender' => 'Жынысы',
                'male' => 'Ер',
                'female' => 'Әйел',
                'ready' => 'Жазуға дайын',
                'repeat_word' => 'Сөзді қайталаңыз',
                'hint_ready' => 'Жазуды бастау үшін микрофонды басыңыз',
                'skip' => 'Сөзді өткізіп жіберу',
                'child_name_placeholder' => 'Мысалы: Анар',
                'child_id_placeholder' => 'Мысалы: 101',
                'child_age_placeholder' => 'Мысалы: 6',
                'disorder_type' => 'Бұзылыс түрі',
                'disorder_type_placeholder' => 'Бұзылыс түрін таңдаңыз',
                'disorder_zrr' => 'Тілі кеш шығуы (ЗРР)',
                'disorder_dyslalia' => 'Дислалия',
                'disorder_onr' => 'Тіл мүкістігі (ОНР)',
                'disorder_dysarthria' => 'Дизартрия',
                'disorder_stuttering' => 'Тұтығу (кекештену)',
                'recording_status' => 'Жазу жүруде...',
                'recording_hint' => 'Анық сөйлеңіз. Тоқтату үшін қайта басыңыз.',
                'analyzing_status' => 'AI талдауда...',
                'analyzing_hint' => 'Айтылуды талдап жатырмыз...',
                'report_status' => 'Есеп жасалуда...',
                'report_hint' => 'AI-диагностиканы жинақтау...',
                'mic_error_https' => 'Бет HTTPS арқылы ашылуы керек. Әйтпесе браузер микрофонды блоктайды.',
                'mic_error_browser' => 'Браузер жазуды қолдамайды. Chrome, Edge, Firefox немесе Safari қолданыңыз.',
                'mic_error_recorder' => 'MediaRecorder бұл браузерде қолданылмайды.',
                'mic_error_denied' => 'Микрофонға рұқсат берілмеген. Браузер параметрлерінде рұқсат беріңіз.',
                'mic_error_not_found' => 'Микрофон табылмады. Құрылғыны қосып, бетті жаңартыңыз.',
                'mic_error_empty' => 'Жазу бос болып шықты. Қайта көріңіз.',
                'mic_error_generic' => 'Микрофон қатесі',
                'complete_error' => 'Аяқтау қатесі',
            ],
        ],
        'en' => [
            'home' => 'Home',
            'diagnosis' => 'Diagnosis',
            'results' => 'Results',
            'recommendations' => 'Exercises',
            'therapist' => 'Therapist Panel',
            'courses' => 'Courses',
            'dataset' => 'Dataset',
            'dataset_history' => 'Dataset History',
            'lang_switch' => 'RU',
            'subtitle' => 'AI speech disorder diagnosis',
            'footer' => '© 2026 TilDamu.kz. Professional speech development support for children.',
            'common' => [
                'login' => 'Sign In',
                'logout' => 'Sign Out',
                'register' => 'Sign Up',
                'email' => 'Email',
                'password' => 'Password',
                'phone' => 'Phone',
                'full_name' => 'Full Name',
                'save' => 'Save',
                'send' => 'Send',
                'watch' => 'Watch',
                'download_pdf' => 'Download PDF',
                'child' => 'Child',
                'free_start' => 'Start for Free',
                'notes_saved' => 'Saved',
                'priority_low' => 'Priority: Low',
                'priority_medium' => 'Priority: Medium',
                'priority_high' => 'Priority: High',
                'avatar' => 'Avatar',
                'optional' => 'optional',
            ],
            'auth' => [
                'login_title' => 'Sign in to TilDamu',
                'login_subtitle' => 'Sign in to continue',
                'register_title' => 'Sign Up',
                'register_subtitle' => 'Create your TilDamu.kz account',
                'no_account' => "Don't have an account?",
                'has_account' => 'Already have an account?',
                'role_title' => 'I am registering as',
                'role_parent' => 'Parent',
                'role_patient' => 'Client / parent',
                'role_therapist' => 'Therapist',
                'patient_notice' => 'Registration is open for clients. Admins, developers, and therapists are created by an admin.',
                'avatar_title' => 'Profile Avatar',
                'avatar_hint' => 'JPG, JPEG and PNG supported, up to 5 MB.',
                'child_name' => 'Child Name',
                'child_age' => 'Child Age',
                'password_confirm' => 'Confirm Password',
                'password_min' => 'Minimum 6 characters',
                'upload_avatar' => 'Upload Avatar',
                'fill_all' => 'Please fill all fields.',
                'invalid_credentials' => 'Invalid email or password.',
                'enter_name' => 'Enter your name.',
                'invalid_email' => 'Enter a valid email.',
                'password_length' => 'Password must be at least 6 characters.',
                'password_mismatch' => 'Passwords do not match.',
                'enter_child_name' => 'Enter child name.',
                'email_exists' => 'A user with this email already exists.',
                'register_error' => 'Registration error. Check DB connection.',
            ],
            'chat' => [
                'empty' => 'Message is empty.',
                'placeholder' => 'E.g.: how to help my child with the R sound at home?',
                'welcome' => 'Hello! I can explain results, suggest home exercises and help parents understand the next step.',
                'temporary_unavailable' => 'Service temporarily unavailable. Try again later.',
                'failed_reply' => 'Could not get a reply.',
                'title' => 'Parent Chat Bot',
                'subtitle' => 'Ask questions about your child\'s speech and home practice.',
            ],
            'results_page' => [
                'ai_explanation' => 'AI Explanation for Parents',
                'good_news' => 'Good News',
                'good_news_text' => 'With proper corrective work, most children fully fix their pronunciation within 3-6 months.',
            ],
            'therapist_page' => [
                'ai_recommendations' => 'AI Recommendations for Therapist',
                'notes_saved' => 'Saved',
            ],
            'dataset_page' => [
                'title' => 'Dataset Collection',
                'subtitle' => 'Help us collect data to improve AI',
                'info_title' => 'Child Information',
                'child_name' => 'Child Name',
                'child_id' => 'Child ID',
                'child_age' => 'Age',
                'gender' => 'Gender',
                'male' => 'Male',
                'female' => 'Female',
                'ready' => 'Ready to record',
                'repeat_word' => 'Repeat the word',
                'hint_ready' => 'Click the microphone to start recording',
                'skip' => 'Skip word',
                'child_name_placeholder' => 'E.g.: Anar',
                'child_id_placeholder' => 'E.g.: 101',
                'child_age_placeholder' => 'E.g.: 6',
                'disorder_type' => 'Impairment Type',
                'disorder_type_placeholder' => 'Select impairment type',
                'disorder_zrr' => 'SRD (Speech Retardation)',
                'disorder_dyslalia' => 'Dyslalia',
                'disorder_onr' => 'GSD (General Speech Disorder)',
                'disorder_dysarthria' => 'Dysarthria',
                'disorder_stuttering' => 'Stuttering',
                'recording_status' => 'Recording...',
                'recording_hint' => 'Speak clearly. Click again to stop.',
                'analyzing_status' => 'AI analyzing...',
                'analyzing_hint' => 'Analyzing pronunciation...',
                'report_status' => 'Generating report...',
                'report_hint' => 'Gathering AI diagnostics...',
                'mic_error_https' => 'The page must be opened via HTTPS. Otherwise, the browser blocks the microphone.',
                'mic_error_browser' => 'The browser does not support recording. Use Chrome, Edge, Firefox, or Safari.',
                'mic_error_recorder' => 'MediaRecorder is not supported in this browser.',
                'mic_error_denied' => 'Microphone access denied. Enable the microphone in your browser settings.',
                'mic_error_not_found' => 'Microphone not found. Connect a device and refresh the page.',
                'mic_error_empty' => 'The recording is empty. Please try again.',
                'mic_error_generic' => 'Microphone error',
                'complete_error' => 'Completion error',
            ],
            'recommendations_page' => [
                'completed' => 'Done',
                'bot_title' => 'Parent Chat Bot',
            ],
            'diagnosis_page' => [
                'title' => 'AI Speech Diagnosis',
                'subtitle' => 'Repeat the words clearly and calmly',
                'progress' => 'Test Progress',
                'ready' => 'Ready to record',
                'repeat_word' => 'Repeat the word',
                'hint_ready' => 'Press the microphone to start recording',
                'skip' => 'Skip word',
                'tips_title' => 'Tips for accurate diagnosis',
                'tip1' => 'Be in a quiet room without background noise',
                'tip2' => 'Hold the microphone 15-20 cm from the child\'s mouth',
                'tip3' => 'Speak at a natural pace, don\'t rush',
                'tip4' => 'If the child is tired, take a break',
                'listen' => 'Listen',
            ],
            'courses_page' => [
                'title' => 'Treatment Courses',
                'subtitle' => 'Online therapy courses with exercises and materials',
                'create' => 'Create Course',
                'no_courses' => 'No courses yet',
                'no_courses_desc' => 'Create your first treatment course.',
                'form_title' => 'Course Title',
                'form_desc' => 'Description',
                'form_content' => 'Course Content',
                'form_media' => 'Media Files',
                'form_media_hint' => 'Upload images, audio or video (max 50 MB each)',
                'form_submit' => 'Create Course',
                'lessons' => 'lessons',
                'by' => 'by',
            ],
            'admin_users_page' => [
                'nav' => 'Users',
                'eyebrow' => 'Access',
                'title' => 'Users',
                'subtitle' => 'Admins create staff accounts, while developers can view the list.',
                'total' => 'Total',
                'create_title' => 'Create user',
                'create_hint' => 'Clients can register themselves; staff roles are assigned by an admin.',
                'role' => 'Role',
                'create' => 'Create',
                'table_title' => 'User list',
                'admin_hint' => 'Admin can create users, change roles, and disable accounts.',
                'readonly_hint' => 'Read-only mode: management is available only to admins.',
                'empty_text' => 'No users yet.',
                'status' => 'Status',
                'last_login' => 'Last login',
                'actions' => 'Actions',
                'active' => 'Active',
                'disabled' => 'Disabled',
                'new_password' => 'New password',
                'disable' => 'Disable',
                'enable' => 'Enable',
                'created' => 'User created.',
                'updated' => 'User updated.',
                'email_exists' => 'A user with this email already exists.',
                'save_error' => 'Could not save the user.',
                'invalid_role' => 'Invalid user role.',
                'not_found' => 'User not found.',
                'self_disable_forbidden' => 'You cannot disable your own account.',
            ],
        ],
    ];
}

function t(?string $key = null): array|string
{
    $lang = current_language();
    $strings = translations()[$lang] ?? translations()['ru'];
    if ($key === null) {
        return $strings;
    }
    return $strings[$key] ?? $key;
}

function tr(string $key, ?string $default = null): string
{
    $lang = current_language();
    $strings = translations()[$lang] ?? translations()['ru'];
    $value = $strings;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default ?? $key;
        }
        $value = $value[$segment];
    }
    return is_string($value) ? $value : ($default ?? $key);
}

function current_page_key(): string
{
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
    return match ($path) {
        '', 'index.php' => 'home',
        'diagnosis', 'diagnosis.php' => 'diagnosis',
        'results', 'results.php' => 'results',
        'recommendations', 'recommendations.php' => 'recommendations',
        'therapist', 'therapist.php' => 'therapist',
        'courses', 'courses.php' => 'courses',
        'course-create', 'course-create.php' => 'courses',
        'dataset', 'dataset.php' => 'dataset',
        'dataset-history', 'dataset-history.php' => 'dataset_history',
        'admin-users', 'admin-users.php' => 'admin_users',
        default => $path,
    };
}

function is_active_page(string $page): bool
{
    return current_page_key() === $page;
}

function lang_switch_url(): string
{
    $currentLang = current_language();
    $newLang = match ($currentLang) {
        'ru' => 'kk',
        'kk' => 'en',
        'en' => 'ru',
        default => 'ru',
    };
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $query = $_GET;
    $query['lang'] = $newLang;
    return $path . '?' . http_build_query($query);
}

function lang_url(string $code): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $query = $_GET;
    $query['lang'] = $code;
    return $path . '?' . http_build_query($query);
}

function ui_icon(string $name, string $class = 'w-5 h-5', string $extraAttributes = ''): string
{
    $icons = [
        'robot' => '<rect width="18" height="10" x="3" y="11" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" x2="8" y1="16" y2="16"/><line x1="16" x2="16" y1="16" y2="16"/><path d="M7 3h10"/>',
        'sparkles' => '<path d="M12 3l1.6 4.4L18 9l-4.4 1.6L12 15l-1.6-4.4L6 9l4.4-1.6L12 3z"/><path d="M19 14l.9 2.1L22 17l-2.1.9L19 20l-.9-2.1L16 17l2.1-.9L19 14z"/><path d="M5 14l.9 2.1L8 17l-2.1.9L5 20l-.9-2.1L2 17l2.1-.9L5 14z"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'check-circle' => '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
        'lightbulb' => '<path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12c.5.4 1 1.2 1 2h6c0-.8.5-1.6 1-2A7 7 0 0 0 12 2Z"/>',
        'star' => '<path d="m12 3.5 2.7 5.4 6 .9-4.3 4.2 1 6-5.4-2.8-5.4 2.8 1-6L3.3 9.8l6-.9L12 3.5Z"/>',
        'bolt' => '<path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z"/>',
        'trophy' => '<path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v3a5 5 0 0 1-10 0V4Z"/><path d="M5 5H3v1a4 4 0 0 0 4 4"/><path d="M19 5h2v1a4 4 0 0 1-4 4"/>',
        'medal' => '<circle cx="12" cy="14" r="5"/><path d="m7 3 5 6 5-6"/>',
        'camera-plus' => '<path d="M14.5 4H17a2 2 0 0 1 2 2v2.5"/><path d="M5 7h2l1.5-2h7L17 7h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z"/><circle cx="12" cy="13" r="3.5"/><path d="M19 3v4"/><path d="M17 5h4"/>',
        'user' => '<path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/>',
        'shield' => '<path d="M12 3l7 3v5c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6l7-3Z"/>',
    ];
    $body = $icons[$name] ?? $icons['sparkles'];
    return '<svg class="' . e($class) . '" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ' . $extraAttributes . '>' . $body . '</svg>';
}

function auth_normalize_role(?string $role): string
{
    $normalized = \App\Models\User::normalizeRole($role);
    if ($normalized !== null) {
        return $normalized;
    }

    $fallback = strtolower(trim((string) $role));
    return $fallback !== '' ? $fallback : 'guest';
}

function auth_role_label(?string $role = null): string
{
    $role = auth_normalize_role($role ?? auth_role());
    $labels = [
        'ru' => [
            'patient' => 'Клиент',
            'therapist' => 'Логопед',
            'admin' => 'Администратор',
            'developer' => 'Разработчик',
            'researcher' => 'Исследователь',
            'guest' => 'Гость',
        ],
        'kk' => [
            'patient' => 'Клиент',
            'therapist' => 'Логопед',
            'admin' => 'Әкімші',
            'developer' => 'Әзірлеуші',
            'researcher' => 'Зерттеуші',
            'guest' => 'Қонақ',
        ],
        'en' => [
            'patient' => 'Client',
            'therapist' => 'Therapist',
            'admin' => 'Admin',
            'developer' => 'Developer',
            'researcher' => 'Researcher',
            'guest' => 'Guest',
        ],
    ];

    $lang = current_language();
    return $labels[$lang][$role] ?? $labels['ru'][$role] ?? $role;
}

function auth_role_options(): array
{
    $roles = [];
    foreach (\App\Models\User::adminCreatableRoles() as $role) {
        $roles[$role] = auth_role_label($role);
    }

    return $roles;
}

function auth_role_matches(string $role, array $allowedRoles): bool
{
    $role = auth_normalize_role($role);
    $allowed = array_map('auth_normalize_role', $allowedRoles);
    return in_array($role, $allowed, true);
}

function auth_check(): bool
{
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

function auth_user(): ?array
{
    static $cached = null;
    if (!auth_check()) {
        return null;
    }
    if ($cached !== null && ($cached['id'] ?? 0) === $_SESSION['user_id']) {
        return $cached;
    }
    $cached = (new \App\Models\User())->findById((int) $_SESSION['user_id']);
    if (!$cached || !((int) ($cached['is_active'] ?? 0))) {
        unset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user_name']);
        $cached = null;
        return null;
    }
    $_SESSION['user_role'] = auth_normalize_role((string) ($cached['role'] ?? ''));
    $_SESSION['user_name'] = (string) ($cached['full_name'] ?? '');
    return $cached;
}

function auth_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function auth_role(): string
{
    return auth_normalize_role((string) ($_SESSION['user_role'] ?? 'guest'));
}

function auth_name(): string
{
    return (string) ($_SESSION['user_name'] ?? '');
}

function auth_is(string $role): bool
{
    return auth_role_matches(auth_role(), [$role]);
}

function auth_require(string ...$roles): void
{
    if (!auth_check()) {
        $_SESSION['auth_error'] = $roles === []
            ? tr('auth.fill_all', 'Для доступа необходимо войти в аккаунт.')
            : tr('auth.fill_all', 'Для доступа необходим вход в аккаунт с нужной ролью.');
        $_SESSION['auth_intended'] = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        redirect('/login.php');
    }
    if (auth_user() === null) {
        $_SESSION['auth_error'] = tr('auth.invalid_credentials', 'Сессия недействительна. Войдите снова.');
        $_SESSION['auth_intended'] = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        redirect('/login.php');
    }
    if ($roles !== [] && !auth_role_matches(auth_role(), $roles)) {
        http_response_code(403);
        echo current_language() === 'kk' ? '403 Қол жеткізуге тыйым салынған' : '403 Доступ запрещён';
        exit;
    }
}

function auth_has_any_role(string ...$roles): bool
{
    return auth_check() && ($roles === [] || auth_role_matches(auth_role(), $roles));
}

function auth_initials(?string $name = null): string
{
    $name = $name ?: auth_name();
    $parts = explode(' ', trim($name));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }
    return $initials ?: '?';
}

function auth_avatar_url(?array $user = null): ?string
{
    $user ??= auth_user();
    $avatar = trim((string) ($user['avatar_url'] ?? ''));
    if ($avatar === '') {
        return null;
    }
    if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://') || str_starts_with($avatar, '/')) {
        return $avatar;
    }
    return url($avatar);
}
