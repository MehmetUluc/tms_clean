<x-filament-panels::page>
    <style>
        /* Modern booking wizard styles */
        .booking-wizard-v3 {
            --primary: rgb(251, 146, 60);
            --primary-hover: rgb(234, 88, 12);
        }
        
        /* Search step background with subtle pattern */
        .search-step-section {
            position: relative;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(251, 146, 60, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(251, 146, 60, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(251, 146, 60, 0.02) 0%, transparent 50%);
            background-size: 100% 100%;
        }
        
        /* Light decorative border */
        .search-step-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(251, 146, 60, 0.3), 
                rgba(251, 146, 60, 0.5), 
                rgba(251, 146, 60, 0.3), 
                transparent
            );
            border-radius: 2px;
        }
        
        /* Destination search field styling */
        .destination-search-field {
            font-size: 1.125rem !important;
        }
        
        .destination-search-field input {
            height: 3rem !important;
            font-size: 1rem !important;
        }
        
        /* Guest counter styling */
        .guest-counter input[type="number"] {
            text-align: center !important;
            -moz-appearance: textfield;
            font-weight: 600;
            font-size: 1.125rem;
        }
        
        .guest-counter input[type="number"]::-webkit-inner-spin-button,
        .guest-counter input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* Action buttons (increment/decrement) */
        .fi-ac-btn-action {
            width: 2.5rem !important;
            height: 2.5rem !important;
            border-radius: 50% !important;
            transition: all 0.2s ease;
        }
        
        .fi-ac-btn-action:hover {
            transform: scale(1.1);
            background-color: var(--primary) !important;
            color: white !important;
        }
        
        .fi-ac-btn-action:active {
            transform: scale(0.95);
        }
        
        /* Date picker enhancements */
        .fi-fo-date-picker {
            transition: all 0.2s ease;
        }
        
        .fi-fo-date-picker:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Section styling */
        .fi-section {
            border-radius: 1rem !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Wizard step indicators */
        .fi-wizard-header {
            background: linear-gradient(to right, #f3f4f6, #e5e7eb);
            padding: 1.5rem !important;
            border-radius: 1rem 1rem 0 0;
        }
        
        .dark .fi-wizard-header {
            background: linear-gradient(to right, #1f2937, #111827);
        }
        
        /* Step icon styling */
        .fi-wizard-step-icon {
            transition: all 0.3s ease;
        }
        
        .fi-wizard-step-icon[aria-current="true"] {
            transform: scale(1.1);
            box-shadow: 0 0 0 4px rgba(251, 146, 60, 0.2);
        }
        
        /* Hotel cards */
        .hotel-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .hotel-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .hotel-card.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.1);
        }
        
        /* Room selection */
        .room-card {
            transition: all 0.2s ease;
        }
        
        .room-card:hover {
            border-color: var(--primary);
        }
        
        /* Price tag */
        .price-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
        }
        
        /* Filters sidebar */
        .filters-sidebar {
            position: sticky;
            top: 1rem;
        }
        
        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .filters-sidebar {
                position: relative;
            }
        }
        
        /* Hotel Selection Step Enhancements */
        .hotel-selection-step {
            position: relative;
            min-height: 600px;
        }
        
        /* Glass Card Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }
        
        .dark .glass-card {
            background: rgba(31, 41, 55, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        /* Price Tag Simple */
        .price-tag-simple {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.125rem;
        }
        
        /* Price Quick Options */
        .price-quick-option {
            flex: 1;
            padding: 0.375rem 0.75rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.2s ease;
        }
        
        .dark .price-quick-option {
            background: #374151;
            border-color: #4b5563;
            color: #9ca3af;
        }
        
        .price-quick-option:hover {
            background: rgba(251, 146, 60, 0.1);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
        }
        
        .price-quick-option:active {
            transform: translateY(0);
        }
        
        
        /* Star Filter Pills */
        .star-filter-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            border: 2px solid transparent;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .star-filter-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .star-filter-pill:has(input:checked) {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
            border-color: var(--primary);
        }
        
        /* Sort Dropdown */
        .sort-dropdown select {
            padding: 0.5rem 2.5rem 0.5rem 1rem;
            border-radius: 0.75rem;
            border: 2px solid #e5e7eb;
            background-color: white;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .sort-dropdown select:hover {
            border-color: var(--primary);
        }
        
        /* Selected Rooms Summary */
        .selected-rooms-summary {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            border: 2px solid rgba(251, 146, 60, 0.2);
        }
        
        /* Pulse Dot Animation */
        .pulse-dot {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }
        
        /* Enhanced Hotel Card */
        .hotel-card-modern {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .dark .hotel-card-modern {
            background: #1f2937;
            border-color: rgba(255, 255, 255, 0.05);
        }
        
        .hotel-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Elegant Gradient */
        .elegant-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Premium Badge */
        .premium-badge {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Discount Badge */
        .discount-badge {
            background: #ef4444;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Rating Badge */
        .rating-badge {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 0.5rem 0.75rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #f59e0b;
            font-size: 0.875rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Amenity Badge */
        .amenity-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            background: rgba(229, 231, 235, 0.5);
            color: #4b5563;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .dark .amenity-badge {
            background: rgba(55, 65, 81, 0.5);
            color: #d1d5db;
        }
        
        .amenity-badge:hover {
            background: rgba(251, 146, 60, 0.1);
            color: var(--primary);
        }
        
        /* Modern Buttons */
        .modern-button-primary {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%);
            color: white;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(251, 146, 60, 0.3);
        }
        
        .modern-button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 12px -2px rgba(251, 146, 60, 0.4);
        }
        
        .modern-button-secondary {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: white;
            color: #6b7280;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .dark .modern-button-secondary {
            background: #374151;
            color: #d1d5db;
            border-color: #4b5563;
        }
        
        .modern-button-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        
        /* Room Card Modern */
        .room-card-modern {
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.2s ease;
        }
        
        .dark .room-card-modern {
            background: #111827;
            border-color: #374151;
        }
        
        .room-card-modern:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Room Amenity Tags */
        .room-amenity-tag {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            color: #6b7280;
        }
        
        .dark .room-amenity-tag {
            background: #1f2937;
            border-color: #374151;
            color: #9ca3af;
        }
        
        /* Rate Plan Card */
        .rate-plan-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.2s ease;
        }
        
        .dark .rate-plan-card {
            background: #1f2937;
            border-color: #374151;
        }
        
        .rate-plan-card:hover {
            border-color: rgba(251, 146, 60, 0.5);
        }
        
        .rate-plan-card.selected {
            background: rgba(251, 146, 60, 0.05);
            border-color: var(--primary);
        }
        
        /* Rate Plan Button */
        .rate-plan-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: #10b981;
            color: white;
            border: 2px solid transparent;
        }
        
        .rate-plan-button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }
        
        .rate-plan-button.selected {
            background: #ef4444;
        }
        
        .rate-plan-button.selected:hover {
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
        }
        
        /* No Results Container */
        .no-results-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 600px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
        }
        
        .dark .no-results-container {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
        }
        
        /* Background Pattern */
        .no-results-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.05;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(251, 146, 60, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(236, 72, 153, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(59, 130, 246, 0.3) 0%, transparent 50%);
        }
        
        .no-results-content {
            position: relative;
            text-align: center;
            padding: 4rem 2rem;
            max-width: 700px;
            z-index: 1;
        }
        
        /* Sad Face Wrapper */
        .sad-face-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        /* Sad Face Elegant */
        .sad-face-elegant {
            color: #6b7280;
            margin: 0 auto;
            animation: float 4s ease-in-out infinite;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.1));
        }
        
        .dark .sad-face-elegant {
            color: #9ca3af;
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg);
            }
            25% {
                transform: translateY(-10px) rotate(-2deg);
            }
            75% {
                transform: translateY(-10px) rotate(2deg);
            }
        }
        
        /* Decorative Dots */
        .decorative-dots {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            pointer-events: none;
        }
        
        .decorative-dots span {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(251, 146, 60, 0.3);
            border-radius: 50%;
            animation: orbit 20s linear infinite;
        }
        
        .decorative-dots span:nth-child(1) {
            top: 0;
            left: 50%;
            animation-delay: 0s;
        }
        
        .decorative-dots span:nth-child(2) {
            top: 50%;
            right: 0;
            animation-delay: -5s;
            background: rgba(236, 72, 153, 0.3);
        }
        
        .decorative-dots span:nth-child(3) {
            bottom: 0;
            left: 50%;
            animation-delay: -10s;
            background: rgba(59, 130, 246, 0.3);
        }
        
        @keyframes orbit {
            from {
                transform: rotate(0deg) translateX(125px) rotate(0deg);
            }
            to {
                transform: rotate(360deg) translateX(125px) rotate(-360deg);
            }
        }
        
        /* Enhanced Suggestion Pills */
        .suggestion-pill-enhanced {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 2rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: #4b5563;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .dark .suggestion-pill-enhanced {
            background: #374151;
            border-color: #4b5563;
            color: #d1d5db;
        }
        
        .suggestion-pill-enhanced:hover {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -5px rgba(251, 146, 60, 0.3);
        }
        
        .suggestion-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            background: rgba(251, 146, 60, 0.1);
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .suggestion-pill-enhanced:hover .suggestion-icon {
            background: var(--primary);
            color: white;
            transform: rotate(10deg);
        }
        
        .suggestion-pill-enhanced:hover .suggestion-icon svg {
            color: white;
        }
        
        /* Back Button Enhanced */
        .back-button-enhanced {
            display: inline-flex;
            align-items: center;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            color: white;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.2);
            border: 2px solid transparent;
        }
        
        .back-button-enhanced:hover {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.3);
        }
        
        .dark .back-button-enhanced {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }
        
        .dark .back-button-enhanced:hover {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        }
        
        /* Suggestions Wrapper */
        .suggestions-wrapper {
            margin-top: 2rem;
        }
        
        /* Enhanced Room Card Styles */
        .room-card-unavailable {
            position: relative;
            overflow: hidden;
        }
        
        .room-card-unavailable::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.05);
            pointer-events: none;
            z-index: 1;
        }
        
        /* Availability Indicators */
        .availability-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .availability-low {
            background: rgba(251, 146, 60, 0.1);
            color: #ea580c;
            border: 1px solid rgba(251, 146, 60, 0.2);
        }
        
        .dark .availability-low {
            background: rgba(251, 146, 60, 0.2);
            color: #fb923c;
            border-color: rgba(251, 146, 60, 0.3);
        }
        
        .availability-medium {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        
        .dark .availability-medium {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .availability-good {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .dark .availability-good {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.3);
        }
        
        .availability-none {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .dark .availability-none {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        /* Enhanced Price Display */
        .rate-plan-card .text-3xl {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Child Policy Badge */
        .child-policy-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.625rem;
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .dark .child-policy-badge {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }
        
        /* Price Breakdown Section */
        .price-breakdown {
            position: relative;
            padding-left: 1rem;
        }
        
        .price-breakdown::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, var(--primary), rgba(251, 146, 60, 0.3));
            border-radius: 1.5px;
        }
        
        /* Animate unavailable room overlay */
        @keyframes fadeInOverlay {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .room-card-unavailable .absolute {
            animation: fadeInOverlay 0.3s ease-out;
        }
    </style>
    
    <div class="booking-wizard-v3">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Book Your Perfect Stay</h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">Find and book from thousands of unique hotels</p>
        </div>
        
        <form wire:submit="create">
            {{ $this->form }}
        </form>
    </div>
    
    @push('scripts')
        <script>
            // Real-time search functionality
            document.addEventListener('livewire:init', function () {
                // Handle destination search
                Livewire.on('show-suggestions', (suggestions) => {
                    // Display search suggestions
                });
                
                // Handle hotel selection
                Livewire.on('hotel-selected', ({hotelId}) => {
                    // Scroll to room selection
                    document.getElementById(`hotel-${hotelId}-rooms`)?.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                });
                
                // Handle room addition
                Livewire.on('room-added', () => {
                    // Update UI to show room in cart
                });
            });
        </script>
    @endpush
</x-filament-panels::page>