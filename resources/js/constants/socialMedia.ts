/**
 * 社交媒體連結常數
 */
export const SOCIAL_MEDIA_LINKS = {
    instagram: {
        url: 'https://www.instagram.com/lei171717/',
        label: 'Instagram',
        icon: 'instagram'
    },
    linkedin: {
        url: 'https://www.linkedin.com/in/%E7%BF%94%E9%BA%9F-%E9%9B%B7-350597199/',
        label: 'LinkedIn',
        icon: 'linkedin'
    },
    threads: {
        url: 'https://www.threads.com/@lei171717?hl=zh-tw',
        label: 'Threads',
        icon: 'threads'
    }
} as const;

/**
 * 社交媒體連結陣列（用於迭代）
 */
export const SOCIAL_MEDIA_LIST = Object.values(SOCIAL_MEDIA_LINKS); 