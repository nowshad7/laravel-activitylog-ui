import { defineConfig } from 'vitepress'

// Project site served from https://nowshad7.github.io/laravel-activitylog-ui/
export default defineConfig({
  title: 'Laravel Activity Log UI',
  description: 'A beautiful Tailwind CSS dashboard for Spatie Laravel Activitylog — browse, filter, diff, analyse and export your audit trail.',
  base: '/laravel-activitylog-ui/',
  lastUpdated: true,
  cleanUrls: true,
  head: [['meta', { name: 'theme-color', content: '#6366f1' }]],
  themeConfig: {
    logo: '/logo.svg',
    nav: [
      { text: 'Guide', link: '/guide/getting-started' },
      { text: 'Configuration', link: '/guide/configuration' },
      { text: 'Changelog', link: 'https://github.com/nowshad7/laravel-activitylog-ui/blob/main/CHANGELOG.md' },
      { text: 'Packagist', link: 'https://packagist.org/packages/nsd7/laravel-activitylog-ui' },
    ],
    sidebar: {
      '/guide/': [
        {
          text: 'Getting started',
          items: [
            { text: 'Introduction', link: '/guide/getting-started' },
            { text: 'Configuration', link: '/guide/configuration' },
          ],
        },
        {
          text: 'Usage',
          items: [
            { text: 'Dashboard', link: '/guide/dashboard' },
            { text: 'Analytics', link: '/guide/analytics' },
            { text: 'Timeline', link: '/guide/timeline' },
            { text: 'Saved views', link: '/guide/saved-views' },
            { text: 'Exports', link: '/guide/exports' },
          ],
        },
        {
          text: 'Advanced',
          items: [
            { text: 'Authorization', link: '/guide/authorization' },
            { text: 'JSON API', link: '/guide/api' },
            { text: 'Localization', link: '/guide/localization' },
          ],
        },
      ],
    },
    socialLinks: [
      { icon: 'github', link: 'https://github.com/nowshad7/laravel-activitylog-ui' },
    ],
    search: { provider: 'local' },
    editLink: {
      pattern: 'https://github.com/nowshad7/laravel-activitylog-ui/edit/main/docs/:path',
      text: 'Edit this page on GitHub',
    },
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © Robiul Hasan Nowshad',
    },
  },
})
