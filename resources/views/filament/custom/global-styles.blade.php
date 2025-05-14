<link rel="stylesheet" href="{{ asset('css/custom/gallery.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom/filepond-grid.css') }}">
<script src="{{ asset('js/custom-upload-fixer.js') }}"></script>
<style>
    /* Modern Table Styling */
    .fi-ta table {
        @apply shadow-sm rounded-xl overflow-hidden border-0;
    }
    
    .fi-ta thead {
        @apply bg-gradient-to-r from-gray-50/80 to-gray-100/80 dark:from-gray-800/80 dark:to-gray-900/80 backdrop-blur-sm;
    }
    
    .fi-ta thead th {
        @apply text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 py-3 px-4;
    }
    
    .fi-ta tbody {
        @apply bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm;
    }
    
    .fi-ta tbody tr {
        @apply hover:bg-gray-50/60 dark:hover:bg-gray-700/60 transition-colors duration-150;
    }
    
    .fi-ta tbody td {
        @apply py-3 px-4 text-sm text-gray-700 dark:text-gray-300;
    }
    
    /* Modern Navigation Styling */
    .fi-topbar, .fi-sidebar {
        @apply backdrop-blur-md bg-white/90 dark:bg-gray-900/90 border-gray-200/50 dark:border-gray-700/50 shadow-sm;
    }
    
    .fi-topbar-item, .fi-sidebar-item {
        @apply transition-colors duration-150 hover:bg-gray-100/50 dark:hover:bg-gray-800/50 rounded-lg;
    }
    
    .fi-sidebar-group-label {
        @apply uppercase text-xs font-bold tracking-wider text-gray-500 dark:text-gray-400 ml-3 my-3;
    }
    
    .fi-sidebar-nav li a.active, .fi-topbar-item-active {
        @apply bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-400 font-medium;
    }
    
    /* Modern Form Controls */
    .fi-input, .fi-select, .fi-textarea {
        @apply rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500;
    }
    
    .fi-btn {
        @apply rounded-lg shadow-sm transition-all duration-150 hover:shadow-md;
    }
    
    .fi-btn-primary {
        @apply bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400;
    }
    
    .fi-btn-success {
        @apply bg-gradient-to-r from-success-600 to-success-500 hover:from-success-500 hover:to-success-400;
    }
    
    .fi-btn-warning {
        @apply bg-gradient-to-r from-warning-600 to-warning-500 hover:from-warning-500 hover:to-warning-400;
    }
    
    .fi-btn-danger {
        @apply bg-gradient-to-r from-danger-600 to-danger-500 hover:from-danger-500 hover:to-danger-400;
    }
    
    /* Card Styling */
    .fi-section {
        @apply rounded-xl shadow-sm hover:shadow-md transition-all duration-300 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6;
    }
    
    /* Tabs Styling */
    .fi-tabs {
        @apply bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-lg shadow-sm border border-gray-200/50 dark:border-gray-700/50 p-1;
    }
    
    .fi-tabs-item {
        @apply rounded-md transition-all duration-150;
    }
    
    .fi-tabs-item-active {
        @apply bg-gradient-to-r from-primary-500/10 to-primary-600/10 text-primary-700 dark:text-primary-400 font-medium;
    }
    
    /* Badge Styling */
    .fi-badge {
        @apply rounded-full font-medium text-xs px-2.5 py-1;
    }
    
    /* Dashboard Styling */
    body {
        @apply bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-950 dark:to-gray-900;
    }
    
    .fi-main {
        @apply p-4;
    }
    
    /* Pagination */
    .fi-pagination {
        @apply flex gap-2 mt-4;
    }
    
    .fi-pagination-item {
        @apply rounded-lg border border-gray-200 dark:border-gray-700 p-2 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors;
    }
    
    /* Hover card animations */
    .fi-card, .fi-section {
        @apply transition-all duration-300 hover:scale-[1.01];
    }
    
    /* Modal styling */
    .fi-modal {
        @apply rounded-xl backdrop-blur-xl bg-white/90 dark:bg-gray-900/90 shadow-xl border border-gray-200/50 dark:border-gray-700/50;
    }
    
    .fi-modal-header {
        @apply border-b border-gray-200/50 dark:border-gray-700/50 px-6 py-4;
    }
    
    .fi-modal-footer {
        @apply border-t border-gray-200/50 dark:border-gray-700/50 px-6 py-4 flex justify-end gap-3;
    }
    
    /* Notification styling */
    .fi-notification {
        @apply rounded-xl shadow-lg bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border border-gray-200/50 dark:border-gray-700/50;
    }
    
    /* Login page styling */
    .fi-simple-layout {
        @apply bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-950 dark:to-gray-900 min-h-screen;
    }
    
    .fi-simple-main {
        @apply flex items-center justify-center min-h-screen;
    }
    
    .fi-simple-card {
        @apply bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl rounded-xl shadow-xl border border-gray-200/50 dark:border-gray-700/50 max-w-lg w-full mx-auto p-8;
    }
    
    /* Doğrudan FilePond Liste Fixi */
    /* Başlıkta tanımlanması daha yüksek CSS önceliğine sahip olur */
    .filepond--list {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 8px !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .filepond--item {
        position: relative !important;
        transform: none !important;
        height: auto !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 0 100% 0 !important; /* Kare yapmak için padding-bottom kullanımı */
        left: 0 !important;
        top: 0 !important;
    }
    
    .filepond--item > fieldset,
    .filepond--item > .filepond--panel,
    .filepond--file-wrapper,
    .filepond--file {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        height: 100% !important;
    }
    
    .filepond--panel-top.filepond--item-panel,
    .filepond--panel-center.filepond--item-panel,
    .filepond--panel-bottom.filepond--item-panel {
        position: absolute !important;
        transform: none !important;
        height: 33.3% !important;
        width: 100% !important;
    }
    
    .filepond--panel-top.filepond--item-panel {
        top: 0 !important;
    }
    
    .filepond--panel-center.filepond--item-panel {
        top: 33.3% !important;
    }
    
    .filepond--panel-bottom.filepond--item-panel {
        top: 66.6% !important;
    }
    
    /* Görüntüleri düzeltme */
    .filepond--image-preview-wrapper,
    .filepond--image-preview,
    .filepond--image-clip {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
    }
    
    /* Dosya bilgisi ve metinleri gizleme */
    .filepond--file-info,
    .filepond--file-status,
    .filepond--file-info-main,
    .filepond--file-info-sub,
    .filepond--file-status-main,
    .filepond--file-status-sub {
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        overflow: hidden !important;
    }
    
    /* Mobil için duyarlı tasarım */
    @media (max-width: 768px) {
        .filepond--list {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    
    @media (max-width: 480px) {
        .filepond--list {
            grid-template-columns: repeat(1, 1fr) !important;
        }
    }
</style>