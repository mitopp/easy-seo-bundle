# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- Initial commit
- feat(manager): Added seo manager
- feat(title): Added title property
- feat(meta-tags): Added meta tags
- feat(profiler): Added collector for profiler
- feat(breadcrumbs): Added breadcrumb manager
- feat(twig): Improved breadcrumbs with active state and JSON-LD support
- feat(manager): English validation messages in `getValidationStatus`
- feat(manager): Added validation of SEO data (title length, meta description, keywords, robots)
- feat(profiler): Display validation status in Symfony Profiler
- feat(twig): Combined `easy_seo_breadcrumbs` and `easy_seo_breadcrumbs_json_ld` into a single call
- fix(twig): Only the last breadcrumb entry receives the CSS class `active` and the attribute `aria-current="page"`
