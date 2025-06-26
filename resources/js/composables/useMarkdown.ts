import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'

/**
 * Markdown 組合式函數
 * 提供安全的 markdown 內容渲染功能
 */
export function useMarkdown() {
    // 初始化 markdown-it 實例
    const md: MarkdownIt = new MarkdownIt({
        // 安全配置
        html: false,        // 禁用原始 HTML 輸入
        xhtmlOut: true,     // 使用 XHTML 格式輸出
        breaks: true,       // 支援換行符轉換為 <br>
        linkify: true,      // 自動將 URL 轉換為連結
        typographer: true,  // 啟用智慧引號和其他印刷符號
        
        // 語法高亮配置
        highlight: function (str: string, lang: string): string {
            if (lang && hljs.getLanguage(lang)) {
                try {
                    return '<pre class="hljs"><code>' +
                           hljs.highlight(str, { language: lang, ignoreIllegals: true }).value +
                           '</code></pre>'
                } catch (__) {}
            }
            
            return '<pre class="hljs"><code>' + md.utils.escapeHtml(str) + '</code></pre>'
        }
    })

    /**
     * 渲染 markdown 內容為 HTML
     * @param content - markdown 內容字串
     * @returns 渲染後的 HTML 字串
     */
    const renderMarkdown = (content: string): string => {
        if (!content) return ''
        
        try {
            return md.render(content)
        } catch (error) {
            console.error('Markdown 渲染錯誤:', error)
            // 發生錯誤時返回轉義後的純文字
            return md.utils.escapeHtml(content)
        }
    }

    /**
     * 渲染 markdown 內容為純文字（用於摘要）
     * @param content - markdown 內容字串
     * @param maxLength - 最大長度
     * @returns 純文字摘要
     */
    const renderMarkdownToText = (content: string, maxLength: number = 150): string => {
        if (!content) return ''
        
        try {
            // 先渲染為 HTML，然後移除 HTML 標籤
            const html = md.render(content)
            const text = html.replace(/<[^>]*>/g, '').trim()
            
            if (text.length <= maxLength) {
                return text
            }
            
            return text.substring(0, maxLength).trim() + '...'
        } catch (error) {
            console.error('Markdown 文字渲染錯誤:', error)
            return content.substring(0, maxLength) + '...'
        }
    }

    return {
        renderMarkdown,
        renderMarkdownToText
    }
} 