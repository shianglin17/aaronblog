transformer 採用 DTO 

解決：
API回應的一致性
資料型別安全
前後端資料格式的標準化
更加模組化的設計

可能的問題：
多層 DTO 轉換麻煩，比如 ARTICLE 裡面有多個 TAG
ORM 到 DTO 轉換還要再另外寫

未來實作方向：
ArticleDTO, PaginatorDTO, transformer 處理 ORM 到 DTO 的轉換

結論：
暫時擱置，先使用 Transformer design pattern