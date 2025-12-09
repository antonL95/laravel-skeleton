<localization-guidelines>
Instead of hard coding strings in the front-end always use use-translation hook and localize them in the backend. use the dot notation to access the string. eg. `const t = useTranslation();` `t('auth.login');`
</localization-guidelines>
