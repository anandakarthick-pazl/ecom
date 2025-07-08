
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreTech Solutions - Enterprise CRM & LMS Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #1e40af;
            --primary-dark: #1e3a8a;
            --secondary-blue: #3b82f6;
            --accent-blue: #60a5fa;
            --light-blue: #dbeafe;
            --gray-900: #111827;
            --gray-800: #1f2937;
            --gray-700: #374151;
            --gray-600: #4b5563;
            --gray-500: #6b7280;
            --gray-400: #9ca3af;
            --gray-300: #d1d5db;
            --gray-200: #e5e7eb;
            --gray-100: #f3f4f6;
            --gray-50: #f9fafb;
            --white: #ffffff;
            --success: #059669;
            --warning: #d97706;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--gray-700);
            background: var(--white);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .container-wide {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Typography */
        .heading-xl {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.1;
            color: var(--gray-900);
        }

        .heading-lg {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2.5rem;
            font-weight: 600;
            line-height: 1.2;
            color: var(--gray-900);
        }

        .heading-md {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.875rem;
            font-weight: 600;
            line-height: 1.3;
            color: var(--gray-900);
        }

        .text-lg {
            font-size: 1.125rem;
            color: var(--gray-600);
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--gray-200);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-blue);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .logo-text {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-item {
            position: relative;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--gray-700);
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 0;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .nav-links a:hover {
            color: var(--primary-blue);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-blue);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Dropdown Menu */
        .dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 200px;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            padding: 0.5rem 0;
        }

        .nav-item:hover .dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown a {
            display: block;
            padding: 0.75rem 1rem;
            color: var(--gray-600);
            text-decoration: none;
            transition: all 0.3s ease;
            border-bottom: none;
        }

        .dropdown a:hover {
            background: var(--gray-50);
            color: var(--primary-blue);
            padding-left: 1.25rem;
        }

        .dropdown a::after {
            display: none;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            backdrop-filter: blur(5px);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray-500);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .modal-close:hover {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .modal-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--gray-700);
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Success Modal Styles */
        .success-modal {
            max-width: 600px;
            border-radius: 20px;
            overflow: visible;
            animation: successModalSlideIn 0.4s ease-out;
        }

        @keyframes successModalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .success-content {
            text-align: center;
            padding: 2rem;
        }

        .success-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: successBounce 0.6s ease-out;
        }

        @keyframes successBounce {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        .success-content h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 0.5rem;
        }

        .success-content h3 {
            font-size: 1.25rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 2rem;
        }

        .success-details {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .success-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .success-item:last-child {
            margin-bottom: 0;
        }

        .success-step-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
            margin-top: 0.25rem;
        }

        .success-step-text {
            flex: 1;
        }

        .success-step-text strong {
            display: block;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .success-step-text span {
            color: var(--gray-600);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .success-footer {
            text-align: center;
        }

        .success-footer p {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
        }

        .success-footer strong {
            color: var(--primary-blue);
        }

        /* Hero Section */
        .hero {
            padding: 8rem 0 6rem;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--light-blue) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-text {
            max-width: 600px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--white);
            color: var(--primary-blue);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .hero-text h1 {
            margin-bottom: 1.5rem;
        }

        .hero-text p {
            font-size: 1.25rem;
            color: var(--gray-600);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .hero-visual {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-dashboard {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            position: relative;
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .dashboard-title {
            font-weight: 600;
            color: var(--gray-900);
        }

        .dashboard-metrics {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .metric-card {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 0.25rem;
        }

        .metric-label {
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .dashboard-chart {
            height: 120px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }

        .chart-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60%;
            background: linear-gradient(to top, rgba(255,255,255,0.1), transparent);
        }

        /* Services Section */
        .services {
            padding: 6rem 0;
            background: white;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .service-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
            text-align: center;
        }

        .service-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-blue);
            transform: translateY(-4px);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
            background: var(--light-blue);
            color: var(--primary-blue);
            line-height: 1;
            overflow: visible;
            padding: 8px;
        }

        /* Products Section */
        .products {
            padding: 6rem 0;
            background: var(--gray-50);
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .section-badge {
            display: inline-block;
            background: var(--light-blue);
            color: var(--primary-blue);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-blue);
            transform: translateY(-4px);
        }

        .product-icon {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            background: var(--light-blue);
            color: var(--primary-blue);
            line-height: 1;
            overflow: visible;
            padding: 8px;
        }

        .product-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .product-description {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            line-height: 1.6;
            font-size: 0.875rem;
            flex-grow: 1;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 2rem;
            flex-grow: 1;
        }

        .feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            color: var(--gray-600);
        }

        .feature-list li::before {
            content: '✓';
            color: var(--success);
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 0.125rem;
            flex-shrink: 0;
        }

        .product-footer {
            margin-top: auto;
            padding-top: 1rem;
        }

        .product-btn {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            background: var(--primary-blue);
            color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .product-btn:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
            transform: translateY(-1px);
        }

        /* Team Section */
        .team {
            padding: 6rem 0;
            background: white;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }

        .team-member {
            text-align: center;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .team-member:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        .member-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }

        .member-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .member-role {
            color: var(--primary-blue);
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .member-bio {
            color: var(--gray-600);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 6rem 0;
            background: var(--gray-50);
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .testimonial-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .testimonial-content {
            font-style: italic;
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--light-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--primary-blue);
        }

        .author-info h4 {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .author-info p {
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        /* Stats Section */
        .stats {
            padding: 4rem 0;
            background: var(--gray-900);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--accent-blue);
            margin-bottom: 0.5rem;
            font-family: 'Space Grotesk', sans-serif;
        }

        .stat-label {
            color: var(--gray-400);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Contact Section */
        .contact {
            padding: 6rem 0;
            background: white;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: start;
        }

        .contact-info h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 2rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .contact-icon {
            width: 48px;
            height: 48px;
            background: var(--light-blue);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--primary-blue);
        }

        .contact-details h4 {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .contact-details p {
            color: var(--gray-600);
        }



        /* About Section */
        .about {
            padding: 6rem 0;
            background: white;
        }

        .about-content {
            max-width: 1000px;
            margin: 0 auto;
        }

        .about-story,
        .about-mission,
        .about-achievements {
            margin-bottom: 4rem;
        }

        .about-story h3,
        .about-mission h3,
        .about-achievements h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
        }

        .about-story p {
            margin-bottom: 1.5rem;
            line-height: 1.7;
            color: var(--gray-600);
        }

        .mission-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .mission-card {
            background: var(--gray-50);
            padding: 2rem;
            border-radius: 12px;
            border-left: 4px solid var(--primary-blue);
        }

        .mission-card h4 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .mission-card p {
            color: var(--gray-600);
            line-height: 1.6;
        }

        .achievements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .achievement-item {
            text-align: center;
            padding: 2rem;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .achievement-item:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .achievement-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .achievement-item h4 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        /* Careers Section */
        .careers {
            padding: 6rem 0;
            background: var(--gray-50);
        }

        .careers-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .careers-culture {
            margin-bottom: 4rem;
        }

        .careers-culture h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 2rem;
            text-align: center;
        }

        .culture-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .culture-item {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .culture-item:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .culture-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .culture-item h4 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .culture-item p {
            color: var(--gray-600);
            line-height: 1.6;
        }

        .job-openings {
            margin-bottom: 4rem;
        }

        .job-openings h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 2rem;
            text-align: center;
        }

        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .job-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .job-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .job-card h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .job-location {
            color: var(--gray-500);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .job-type {
            color: var(--primary-blue);
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .job-description {
            color: var(--gray-600);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .careers-cta {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
        }

        .careers-cta h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .careers-cta p {
            color: var(--gray-600);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* News Section */
        .news {
            padding: 6rem 0;
            background: white;
        }

        .news-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .featured-news {
            margin-bottom: 3rem;
        }

        .news-article {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .news-article:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .news-article.featured {
            background: var(--gray-50);
            border-color: var(--primary-blue);
            padding: 3rem;
        }

        .news-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            align-items: center;
        }

        .news-category {
            background: var(--primary-blue);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .news-date {
            color: var(--gray-500);
            font-size: 0.875rem;
        }

        .news-article h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .news-article h4 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .news-article p {
            color: var(--gray-600);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .news-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .news-link:hover {
            color: var(--primary-dark);
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        /* Support Section */
        .support {
            padding: 6rem 0;
            background: var(--gray-50);
        }

        .support-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .support-options {
            margin-bottom: 4rem;
        }

        .support-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }

        .support-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            text-align: center;
        }

        .support-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .support-card.priority {
            border-color: var(--primary-blue);
            background: var(--light-blue);
        }

        .support-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .support-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .support-card p {
            color: var(--gray-600);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .support-card ul {
            list-style: none;
            margin-bottom: 2rem;
            text-align: left;
        }

        .support-card ul li {
            color: var(--gray-600);
            padding: 0.25rem 0;
            font-size: 0.875rem;
        }

        .support-card ul li::before {
            content: '✓';
            color: var(--success);
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .support-status {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
        }

        .support-status h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
        }

        .status-grid {
            display: grid;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: 8px;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .status-indicator.online {
            background: var(--success);
        }

        .status-indicator.maintenance {
            background: var(--warning);
        }

        .status-text {
            margin-left: auto;
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        .status-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
        }

        /* Locations Section */
        .locations {
            padding: 6rem 0;
            background: white;
        }

        .locations-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .locations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .location-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
            position: relative;
        }

        .location-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .location-card.headquarters {
            border-color: var(--primary-blue);
            background: var(--light-blue);
        }

        .location-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--primary-blue);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .location-flag {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .location-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
        }

        .location-details p {
            margin-bottom: 0.75rem;
            color: var(--gray-600);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .location-details strong {
            color: var(--gray-900);
        }

        .locations-map {
            background: var(--gray-50);
            padding: 3rem;
            border-radius: 12px;
            text-align: center;
        }

        .locations-map h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 2rem;
        }

        .map-placeholder {
            background: white;
            border-radius: 12px;
            padding: 3rem;
            border: 1px solid var(--gray-200);
        }

        .map-content h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 1rem;
        }

        .map-content p {
            color: var(--gray-600);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .coverage-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            flex-wrap: wrap;
        }

        .coverage-stat {
            text-align: center;
        }

        .coverage-stat strong {
            display: block;
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-blue);
            margin-bottom: 0.25rem;
        }

        .coverage-stat span {
            color: var(--gray-600);
            font-size: 0.875rem;
        }
        /* CTA Section */
        .cta {
            padding: 6rem 0;
            background: var(--primary-blue);
            color: white;
            text-align: center;
        }

        .cta h2 {
            color: white;
            margin-bottom: 1rem;
        }

        .cta p {
            font-size: 1.125rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta .btn-primary {
            background: white;
            color: var(--primary-blue);
        }

        .cta .btn-primary:hover {
            background: var(--gray-100);
        }

        .cta .btn-secondary {
            background: transparent;
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
        }

        .cta .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Footer */
        .footer {
            background: var(--gray-900);
            color: var(--gray-300);
            padding: 3rem 0 1rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-brand {
            max-width: 300px;
        }

        .footer-brand .logo {
            margin-bottom: 1rem;
        }

        .footer-brand .logo-text {
            color: white;
        }

        .footer-description {
            color: var(--gray-400);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .footer-section h3 {
            color: white;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: var(--gray-400);
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 0.875rem;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-bottom {
            padding-top: 2rem;
            border-top: 1px solid var(--gray-800);
            text-align: center;
            color: var(--gray-500);
            font-size: 0.875rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
            
            .contact-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .services-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 3rem;
            }

            .heading-xl {
                font-size: 2.5rem;
            }

            .heading-lg {
                font-size: 2rem;
            }

            .hero-buttons {
                justify-content: center;
            }

            .dashboard-metrics {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .modal-content {
                margin: 1rem;
            }

            .success-modal {
                max-width: 90%;
            }

            .success-content {
                padding: 1.5rem;
            }

            .success-details {
                padding: 1.5rem;
            }

            .success-item {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .success-step-icon {
                margin-top: 0;
            }

            .services-grid,
            .products-grid {
                grid-template-columns: 1fr;
            }

            .mission-cards,
            .achievements-grid,
            .culture-grid,
            .jobs-grid,
            .news-grid,
            .support-grid,
            .locations-grid {
                grid-template-columns: 1fr;
            }

            .coverage-stats {
                gap: 2rem;
            }

            .status-item {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .status-text {
                margin-left: 0;
            }

            .location-badge {
                position: static;
                display: inline-block;
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 6rem 0 4rem;
            }

            .products,
            .services,
            .cta,
            .team,
            .testimonials,
            .contact,
            .pricing {
                padding: 4rem 0;
            }

            .heading-xl {
                font-size: 2rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-lg {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-container">
                <a href="#" class="logo">
                    <div class="logo-icon">C</div>
                    <span class="logo-text">CoreTech</span>
                </a>
                <ul class="nav-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#products">Products</a></li>
                    <li class="nav-item">
                        <a href="#company">Company ↓</a>
                        <div class="dropdown">
                            <a href="#about">About Us</a>
                            <a href="#team">Leadership Team</a>
                            <a href="#careers">Careers</a>
                            <a href="#news">News & Press</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="#contact">Contact ↓</a>
                        <div class="dropdown">
                            <a href="#contact">Contact Us</a>
                            <a href="#support">Support</a>
                            <a href="#locations">Office Locations</a>
                        </div>
                    </li>

                    <li><button class="btn btn-primary" onclick="openModal('demoModal')">Request Demo</button></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Demo Request Modal -->
    <div id="demoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Request a Demo</h2>
                <button class="modal-close" onclick="closeModal('demoModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-input" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Business Email *</label>
                        <input type="email" class="form-input" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Company Name *</label>
                            <input type="text" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Company Size</label>
                            <select class="form-select">
                                <option>1-10 employees</option>
                                <option>11-50 employees</option>
                                <option>51-200 employees</option>
                                <option>201-1000 employees</option>
                                <option>1000+ employees</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Industry</label>
                            <select class="form-select">
                                <option>Technology</option>
                                <option>Healthcare</option>
                                <option>Finance</option>
                                <option>Education</option>
                                <option>Manufacturing</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Interested In</label>
                        <select class="form-select">
                            <option>CRM Platform</option>
                            <option>LMS Solution</option>
                            <option>Mobile App Development</option>
                            <option>Security Testing</option>
                            <option>IT Infrastructure</option>
                            <option>AI Development & RPA</option>
                            <option>Data Science</option>
                            <option>Data Analytics</option>
                            <option>All Services</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Additional Information</label>
                        <textarea class="form-textarea" placeholder="Tell us about your specific requirements..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg">Schedule Demo</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content success-modal">
            <div class="modal-body">
                <div class="success-content">
                    <div class="success-icon">✅</div>
                    <h2>Success!</h2>
                    <h3>Your demo request has been submitted successfully</h3>
                    
                    <div class="success-details">
                        <div class="success-item">
                            <div class="success-step-icon">📞</div>
                            <div class="success-step-text">
                                <strong>Our team will connect with you</strong>
                                <span>within 24 hours to schedule your personalized demo</span>
                            </div>
                        </div>
                        
                        <div class="success-item">
                            <div class="success-step-icon">📧</div>
                            <div class="success-step-text">
                                <strong>Confirmation email sent</strong>
                                <span>Check your inbox for demo details and next steps</span>
                            </div>
                        </div>
                        
                        <div class="success-item">
                            <div class="success-step-icon">🎯</div>
                            <div class="success-step-text">
                                <strong>Personalized demo</strong>
                                <span>Tailored to your specific business needs and requirements</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="success-footer">
                        <p>Thank you for choosing <strong>CoreTech Solutions!</strong></p>
                        <button class="btn btn-primary btn-lg" onclick="closeModal('successModal')">Continue Exploring</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <div class="hero-badge">
                        🚀 Trusted by 500+ Enterprise Clients
                    </div>
                    <h1 class="heading-xl">
                        Enterprise-Grade Technology Solutions
                    </h1>
                    <p>
                        Comprehensive CRM, LMS, Mobile Development, Security Testing, and IT Infrastructure solutions. Built for scale, security, and seamless integration.
                    </p>
                    <div class="hero-buttons">
                        <a href="#" class="btn btn-primary btn-lg" onclick="openModal('demoModal')">Get Started</a>
                        <a href="#services" class="btn btn-secondary btn-lg">View Services</a>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-dashboard">
                        <div class="dashboard-header">
                            <h3 class="dashboard-title">Analytics Dashboard</h3>
                            <div style="color: var(--success); font-size: 0.875rem;">● Live</div>
                        </div>
                        <div class="dashboard-metrics">
                            <div class="metric-card">
                                <div class="metric-value">98.7%</div>
                                <div class="metric-label">Uptime</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-value">2.4M</div>
                                <div class="metric-label">Users</div>
                            </div>
                        </div>
                        <div class="dashboard-chart">
                            <div class="chart-overlay"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">About CoreTech</div>
                <h2 class="heading-lg">Transforming Business Through Innovation</h2>
                <p class="text-lg">
                    Founded in 2018, CoreTech Solutions has emerged as a leading provider of enterprise technology solutions, helping businesses worldwide achieve digital transformation and operational excellence.
                </p>
            </div>
            
            <div class="about-content">
                <div class="about-story">
                    <h3>Our Story</h3>
                    <p>
                        CoreTech was born from a simple vision: to bridge the gap between complex enterprise needs and innovative technology solutions. Our founders, with decades of combined experience at Microsoft, Google, and leading startups, recognized that businesses needed a partner who could not only understand their challenges but also deliver scalable, secure, and efficient solutions.
                    </p>
                    <p>
                        What started as a small team of passionate engineers has grown into a comprehensive technology partner serving Fortune 500 companies and innovative startups alike. We've maintained our startup agility while building enterprise-grade capabilities that our clients depend on.
                    </p>
                </div>
                
                <div class="about-mission">
                    <h3>Mission & Vision</h3>
                    <div class="mission-cards">
                        <div class="mission-card">
                            <h4>🎯 Mission</h4>
                            <p>To empower businesses with cutting-edge technology solutions that drive growth, efficiency, and innovation while maintaining the highest standards of security and reliability.</p>
                        </div>
                        <div class="mission-card">
                            <h4>👁️ Vision</h4>
                            <p>To be the most trusted technology partner for enterprises seeking digital transformation, known for our innovation, expertise, and unwavering commitment to client success.</p>
                        </div>
                        <div class="mission-card">
                            <h4>⚡ Values</h4>
                            <p>Innovation, Integrity, Excellence, Collaboration, and Customer Success drive everything we do, from product development to client relationships.</p>
                        </div>
                    </div>
                </div>
                
                <div class="about-achievements">
                    <h3>Our Achievements</h3>
                    <div class="achievements-grid">
                        <div class="achievement-item">
                            <div class="achievement-icon">🏆</div>
                            <h4>Industry Recognition</h4>
                            <p>Winner of "Best Enterprise Software Solution" 2024</p>
                        </div>
                        <div class="achievement-item">
                            <div class="achievement-icon">🔒</div>
                            <h4>Security Certified</h4>
                            <p>SOC 2 Type II, ISO 27001, and GDPR compliant</p>
                        </div>
                        <div class="achievement-item">
                            <div class="achievement-icon">🌍</div>
                            <h4>Global Presence</h4>
                            <p>Serving clients across 50+ countries worldwide</p>
                        </div>
                        <div class="achievement-item">
                            <div class="achievement-icon">💼</div>
                            <h4>Enterprise Partner</h4>
                            <p>Trusted by 95% of Fortune 500 companies</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Careers Section -->
    <section id="careers" class="careers">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Join Our Team</div>
                <h2 class="heading-lg">Build the Future with CoreTech</h2>
                <p class="text-lg">
                    Join a team of passionate innovators, cutting-edge engineers, and visionary leaders who are shaping the future of enterprise technology.
                </p>
            </div>
            
            <div class="careers-content">
                <div class="careers-culture">
                    <h3>Why Work at CoreTech?</h3>
                    <div class="culture-grid">
                        <div class="culture-item">
                            <div class="culture-icon">🚀</div>
                            <h4>Innovation First</h4>
                            <p>Work with cutting-edge technologies and contribute to groundbreaking solutions that impact millions of users worldwide.</p>
                        </div>
                        <div class="culture-item">
                            <div class="culture-icon">🌱</div>
                            <h4>Growth & Learning</h4>
                            <p>Continuous learning opportunities, conference attendance, certification programs, and mentorship from industry experts.</p>
                        </div>
                        <div class="culture-item">
                            <div class="culture-icon">⚖️</div>
                            <h4>Work-Life Balance</h4>
                            <p>Flexible working hours, remote work options, unlimited PTO, and comprehensive health benefits for you and your family.</p>
                        </div>
                        <div class="culture-item">
                            <div class="culture-icon">🎯</div>
                            <h4>Impact & Purpose</h4>
                            <p>Your work directly influences enterprise solutions used by leading companies to transform their operations and serve their customers better.</p>
                        </div>
                    </div>
                </div>
                
                <div class="job-openings">
                    <h3>Current Openings</h3>
                    <div class="jobs-grid">
                        <div class="job-card">
                            <h4>Senior Full-Stack Developer</h4>
                            <p class="job-location">San Francisco, CA • Remote</p>
                            <p class="job-type">Full-time • $120K - $180K</p>
                            <p class="job-description">Lead development of our next-generation CRM platform using React, Node.js, and AWS.</p>
                            <button class="btn btn-primary">Apply Now</button>
                        </div>
                        <div class="job-card">
                            <h4>AI/ML Engineer</h4>
                            <p class="job-location">New York, NY • Hybrid</p>
                            <p class="job-type">Full-time • $140K - $200K</p>
                            <p class="job-description">Build intelligent automation solutions and machine learning models for enterprise clients.</p>
                            <button class="btn btn-primary">Apply Now</button>
                        </div>
                        <div class="job-card">
                            <h4>DevOps Engineer</h4>
                            <p class="job-location">Austin, TX • Remote</p>
                            <p class="job-type">Full-time • $110K - $160K</p>
                            <p class="job-description">Scale our infrastructure, improve deployment pipelines, and ensure 99.9% uptime for enterprise clients.</p>
                            <button class="btn btn-primary">Apply Now</button>
                        </div>
                        <div class="job-card">
                            <h4>UX/UI Designer</h4>
                            <p class="job-location">Seattle, WA • Hybrid</p>
                            <p class="job-type">Full-time • $90K - $130K</p>
                            <p class="job-description">Design intuitive interfaces for complex enterprise software, focusing on user experience and accessibility.</p>
                            <button class="btn btn-primary">Apply Now</button>
                        </div>
                        <div class="job-card">
                            <h4>Security Analyst</h4>
                            <p class="job-location">Boston, MA • Remote</p>
                            <p class="job-type">Full-time • $100K - $150K</p>
                            <p class="job-description">Conduct security assessments, penetration testing, and ensure compliance across all our platforms.</p>
                            <button class="btn btn-primary">Apply Now</button>
                        </div>
                        <div class="job-card">
                            <h4>Product Manager</h4>
                            <p class="job-location">Los Angeles, CA • Hybrid</p>
                            <p class="job-type">Full-time • $130K - $180K</p>
                            <p class="job-description">Drive product strategy and roadmap for our LMS platform, working closely with engineering and design teams.</p>
                            <button class="btn btn-primary">Apply Now</button>
                        </div>
                    </div>
                </div>
                
                <div class="careers-cta">
                    <h3>Don't See Your Role?</h3>
                    <p>We're always looking for talented individuals who share our passion for innovation. Send us your resume and let's start a conversation about how you can contribute to our mission.</p>
                    <button class="btn btn-primary btn-lg" onclick="openModal('demoModal')">Send Your Resume</button>
                </div>
            </div>
        </div>
    </section>

    <!-- News & Press Section -->
    <section id="news" class="news">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Latest Updates</div>
                <h2 class="heading-lg">News & Press</h2>
                <p class="text-lg">
                    Stay updated with CoreTech's latest announcements, product launches, industry insights, and media coverage.
                </p>
            </div>
            
            <div class="news-content">
                <div class="featured-news">
                    <div class="news-article featured">
                        <div class="news-meta">
                            <span class="news-category">Product Launch</span>
                            <span class="news-date">January 15, 2025</span>
                        </div>
                        <h3>CoreTech Launches Revolutionary AI-Powered CRM 3.0</h3>
                        <p>Our latest CRM platform introduces advanced machine learning capabilities that automatically score leads, predict customer behavior, and optimize sales workflows. Early beta users report 40% increase in conversion rates.</p>
                        <a href="#" class="news-link">Read Full Article →</a>
                    </div>
                </div>
                
                <div class="news-grid">
                    <div class="news-article">
                        <div class="news-meta">
                            <span class="news-category">Press Coverage</span>
                            <span class="news-date">January 10, 2025</span>
                        </div>
                        <h4>TechCrunch: "CoreTech Named Top Enterprise Software Company"</h4>
                        <p>Industry recognition for our innovative approach to enterprise solutions and commitment to customer success.</p>
                        <a href="#" class="news-link">Read More →</a>
                    </div>
                    
                    <div class="news-article">
                        <div class="news-meta">
                            <span class="news-category">Company News</span>
                            <span class="news-date">December 28, 2024</span>
                        </div>
                        <h4>CoreTech Completes $50M Series B Funding Round</h4>
                        <p>Funding will accelerate product development and global expansion, with focus on AI and automation technologies.</p>
                        <a href="#" class="news-link">Read More →</a>
                    </div>
                    
                    <div class="news-article">
                        <div class="news-meta">
                            <span class="news-category">Industry Insights</span>
                            <span class="news-date">December 20, 2024</span>
                        </div>
                        <h4>The Future of Enterprise Automation: 2025 Predictions</h4>
                        <p>Our CEO shares insights on emerging trends in AI, RPA, and how businesses can prepare for the automation revolution.</p>
                        <a href="#" class="news-link">Read More →</a>
                    </div>
                    
                    <div class="news-article">
                        <div class="news-meta">
                            <span class="news-category">Product Update</span>
                            <span class="news-date">December 15, 2024</span>
                        </div>
                        <h4>Enhanced Security Features Now Available</h4>
                        <p>New zero-trust architecture and advanced threat detection capabilities across all CoreTech platforms.</p>
                        <a href="#" class="news-link">Read More →</a>
                    </div>
                    
                    <div class="news-article">
                        <div class="news-meta">
                            <span class="news-category">Partnership</span>
                            <span class="news-date">December 5, 2024</span>
                        </div>
                        <h4>Strategic Partnership with Microsoft Azure</h4>
                        <p>Enhanced cloud capabilities and seamless integration with Microsoft's enterprise ecosystem.</p>
                        <a href="#" class="news-link">Read More →</a>
                    </div>
                    
                    <div class="news-article">
                        <div class="news-meta">
                            <span class="news-category">Awards</span>
                            <span class="news-date">November 30, 2024</span>
                        </div>
                        <h4>Winner: Best Enterprise Software Solution 2024</h4>
                        <p>CoreTech's LMS platform recognized for innovation in corporate learning and development.</p>
                        <a href="#" class="news-link">Read More →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Support Section -->
    <section id="support" class="support">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Customer Support</div>
                <h2 class="heading-lg">We're Here to Help</h2>
                <p class="text-lg">
                    Get the assistance you need with our comprehensive support resources, documentation, and expert support team available 24/7.
                </p>
            </div>
            
            <div class="support-content">
                <div class="support-options">
                    <div class="support-grid">
                        <div class="support-card priority">
                            <div class="support-icon">🚨</div>
                            <h3>Priority Support</h3>
                            <p>Enterprise clients get 24/7 priority support with guaranteed response times and dedicated account managers.</p>
                            <ul>
                                <li>Response time: &lt; 1 hour</li>
                                <li>Dedicated account manager</li>
                                <li>Phone & video support</li>
                                <li>Custom SLA agreements</li>
                            </ul>
                            <button class="btn btn-primary">Contact Priority Support</button>
                        </div>
                        
                        <div class="support-card">
                            <div class="support-icon">💬</div>
                            <h3>Live Chat Support</h3>
                            <p>Get instant help from our knowledgeable support team through our live chat system.</p>
                            <ul>
                                <li>Available 24/7</li>
                                <li>Average response: 2 minutes</li>
                                <li>Technical and billing support</li>
                                <li>Multi-language support</li>
                            </ul>
                            <button class="btn btn-secondary">Start Live Chat</button>
                        </div>
                        
                        <div class="support-card">
                            <div class="support-icon">📚</div>
                            <h3>Documentation</h3>
                            <p>Comprehensive guides, API documentation, tutorials, and best practices for all our products.</p>
                            <ul>
                                <li>Step-by-step guides</li>
                                <li>API documentation</li>
                                <li>Video tutorials</li>
                                <li>Best practices</li>
                            </ul>
                            <button class="btn btn-secondary">Browse Docs</button>
                        </div>
                        
                        <div class="support-card">
                            <div class="support-icon">🎓</div>
                            <h3>Training & Webinars</h3>
                            <p>Regular training sessions, webinars, and certification programs to help you get the most from our platforms.</p>
                            <ul>
                                <li>Weekly webinars</li>
                                <li>Certification programs</li>
                                <li>Custom training sessions</li>
                                <li>Best practice workshops</li>
                            </ul>
                            <button class="btn btn-secondary">View Schedule</button>
                        </div>
                        
                        <div class="support-card">
                            <div class="support-icon">🎫</div>
                            <h3>Support Tickets</h3>
                            <p>Submit detailed support requests and track their progress through our support portal.</p>
                            <ul>
                                <li>Detailed issue tracking</li>
                                <li>File attachments support</li>
                                <li>Priority escalation</li>
                                <li>Historical ticket access</li>
                            </ul>
                            <button class="btn btn-secondary">Submit Ticket</button>
                        </div>
                        
                        <div class="support-card">
                            <div class="support-icon">👥</div>
                            <h3>Community Forum</h3>
                            <p>Connect with other CoreTech users, share experiences, and get help from the community.</p>
                            <ul>
                                <li>User community</li>
                                <li>Knowledge sharing</li>
                                <li>Expert moderators</li>
                                <li>Feature discussions</li>
                            </ul>
                            <button class="btn btn-secondary">Join Community</button>
                        </div>
                    </div>
                </div>
                
                <div class="support-status">
                    <h3>System Status</h3>
                    <div class="status-grid">
                        <div class="status-item">
                            <div class="status-indicator online"></div>
                            <span>CRM Platform</span>
                            <span class="status-text">Operational</span>
                        </div>
                        <div class="status-item">
                            <div class="status-indicator online"></div>
                            <span>LMS Solution</span>
                            <span class="status-text">Operational</span>
                        </div>
                        <div class="status-item">
                            <div class="status-indicator online"></div>
                            <span>API Services</span>
                            <span class="status-text">Operational</span>
                        </div>
                        <div class="status-item">
                            <div class="status-indicator maintenance"></div>
                            <span>Analytics Dashboard</span>
                            <span class="status-text">Maintenance (01:00-03:00 UTC)</span>
                        </div>
                    </div>
                    <a href="#" class="status-link">View Full Status Page →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Office Locations Section -->
    <section id="locations" class="locations">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Global Presence</div>
                <h2 class="heading-lg">Our Office Locations</h2>
                <p class="text-lg">
                    With offices around the world, we're always close to our clients and ready to provide local support and expertise.
                </p>
            </div>
            
            <div class="locations-content">
                <div class="locations-grid">
                    <div class="location-card headquarters">
                        <div class="location-badge">Headquarters</div>
                        <div class="location-flag">🇺🇸</div>
                        <h3>San Francisco, USA</h3>
                        <div class="location-details">
                            <p><strong>Address:</strong></p>
                            <p>123 Tech Boulevard, Suite 500<br>San Francisco, CA 94105</p>
                            <p><strong>Contact:</strong></p>
                            <p>+1 (555) 123-4567<br>sf@coretech.com</p>
                            <p><strong>Specialties:</strong></p>
                            <p>Product Development, AI/ML, Executive Leadership</p>
                        </div>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-flag">🇺🇸</div>
                        <h3>New York, USA</h3>
                        <div class="location-details">
                            <p><strong>Address:</strong></p>
                            <p>456 Financial District<br>New York, NY 10004</p>
                            <p><strong>Contact:</strong></p>
                            <p>+1 (555) 234-5678<br>ny@coretech.com</p>
                            <p><strong>Specialties:</strong></p>
                            <p>Sales, Enterprise Solutions, Customer Success</p>
                        </div>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-flag">🇬🇧</div>
                        <h3>London, UK</h3>
                        <div class="location-details">
                            <p><strong>Address:</strong></p>
                            <p>789 Canary Wharf<br>London E14 5AB, UK</p>
                            <p><strong>Contact:</strong></p>
                            <p>+44 20 7123 4567<br>london@coretech.com</p>
                            <p><strong>Specialties:</strong></p>
                            <p>European Operations, Compliance, Data Privacy</p>
                        </div>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-flag">🇸🇬</div>
                        <h3>Singapore</h3>
                        <div class="location-details">
                            <p><strong>Address:</strong></p>
                            <p>321 Marina Bay Financial Centre<br>Singapore 018982</p>
                            <p><strong>Contact:</strong></p>
                            <p>+65 6123 4567<br>singapore@coretech.com</p>
                            <p><strong>Specialties:</strong></p>
                            <p>APAC Operations, Mobile Development, Localization</p>
                        </div>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-flag">🇮🇳</div>
                        <h3>Bangalore, India</h3>
                        <div class="location-details">
                            <p><strong>Address:</strong></p>
                            <p>654 Electronic City Phase 1<br>Bangalore 560100, India</p>
                            <p><strong>Contact:</strong></p>
                            <p>+91 80 2345 6789<br>bangalore@coretech.com</p>
                            <p><strong>Specialties:</strong></p>
                            <p>Software Development, QA Testing, 24/7 Support</p>
                        </div>
                    </div>
                    
                    <div class="location-card">
                        <div class="location-flag">🇦🇺</div>
                        <h3>Sydney, Australia</h3>
                        <div class="location-details">
                            <p><strong>Address:</strong></p>
                            <p>987 Circular Quay<br>Sydney NSW 2000, Australia</p>
                            <p><strong>Contact:</strong></p>
                            <p>+61 2 3456 7890<br>sydney@coretech.com</p>
                            <p><strong>Specialties:</strong></p>
                            <p>Oceania Operations, Cloud Infrastructure, Security</p>
                        </div>
                    </div>
                </div>
                
                <div class="locations-map">
                    <h3>Global Coverage</h3>
                    <div class="map-placeholder">
                        <div class="map-content">
                            <h4>🌍 Serving Clients Worldwide</h4>
                            <p>Our global presence ensures local support and expertise in every major market. Whether you're in North America, Europe, Asia-Pacific, or anywhere in between, CoreTech is here to help.</p>
                            <div class="coverage-stats">
                                <div class="coverage-stat">
                                    <strong>50+</strong>
                                    <span>Countries Served</span>
                                </div>
                                <div class="coverage-stat">
                                    <strong>6</strong>
                                    <span>Global Offices</span>
                                </div>
                                <div class="coverage-stat">
                                    <strong>24/7</strong>
                                    <span>Support Coverage</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Enterprise Clients</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">99.9%</div>
                    <div class="stat-label">System Uptime</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Premium Support</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Global Markets</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Our Services</div>
                <h2 class="heading-lg">Complete Technology Solutions</h2>
                <p class="text-lg">
                    From enterprise software to mobile development and infrastructure - we provide end-to-end technology solutions
                </p>
            </div>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">📊</div>
                    <h3>CRM Platform</h3>
                    <p>Advanced customer relationship management with AI-powered insights and automation</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🎓</div>
                    <h3>LMS Solution</h3>
                    <p>Comprehensive learning management system with interactive content and progress tracking</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">📱</div>
                    <h3>Mobile App Development</h3>
                    <p>Native iOS and Android apps with cross-platform solutions for optimal performance</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🔒</div>
                    <h3>Security Testing</h3>
                    <p>Comprehensive penetration testing, vulnerability assessments, and security audits</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🏗️</div>
                    <h3>IT Infrastructure</h3>
                    <p>Cloud architecture, server management, and scalable infrastructure solutions</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🤖</div>
                    <h3>AI Development & RPA</h3>
                    <p>Custom AI solutions, machine learning models, and Robotic Process Automation for intelligent business workflows</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🔬</div>
                    <h3>Data Science</h3>
                    <p>Advanced data modeling, predictive analytics, and statistical analysis for business insights</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">📈</div>
                    <h3>Data Analytics</h3>
                    <p>Business intelligence, data visualization, and real-time analytics dashboards</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">⚡</div>
                    <h3>DevOps & Automation</h3>
                    <p>CI/CD pipelines, automated deployments, and infrastructure as code</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="products">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Core Products</div>
                <h2 class="heading-lg">Enterprise Software Platforms</h2>
                <p class="text-lg">
                    Powerful, scalable solutions designed to accelerate your business growth and operational efficiency
                </p>
            </div>
            
            <div class="products-grid">
                <!-- CRM Product -->
                <div class="product-card" id="product-crm">
                    <div class="product-icon">📊</div>
                    <h3>Enterprise CRM Platform</h3>
                    <p class="product-description">
                        Advanced customer relationship management system that transforms how you manage leads, track customer journeys, and drive sales growth.
                    </p>
                    <ul class="feature-list">
                        <li>Advanced Lead Scoring & Management</li>
                        <li>360° Customer Journey Mapping</li>
                        <li>Sales Pipeline Automation</li>
                        <li>Real-time Analytics & Reporting</li>
                        <li>Multi-channel Communication Hub</li>
                        <li>Custom Workflow Designer</li>
                        <li>Enterprise Security & Compliance</li>
                    </ul>
                    <div class="product-footer">
                        <button class="product-btn" onclick="openModal('demoModal')">Explore CRM</button>
                    </div>
                </div>

                <!-- LMS Product -->
                <div class="product-card" id="product-lms">
                    <div class="product-icon">🎓</div>
                    <h3>Professional LMS Solution</h3>
                    <p class="product-description">
                        Comprehensive learning management system that empowers organizations to create, deliver, and track engaging educational experiences.
                    </p>
                    <ul class="feature-list">
                        <li>Interactive Course Builder</li>
                        <li>Advanced Student Analytics</li>
                        <li>Certification & Badge Management</li>
                        <li>Video Conferencing Integration</li>
                        <li>Assessment & Quiz Engine</li>
                        <li>Mobile Learning Platform</li>
                        <li>SCORM & xAPI Compliance</li>
                    </ul>
                    <div class="product-footer">
                        <button class="product-btn" onclick="openModal('demoModal')">Explore LMS</button>
                    </div>
                </div>

                <!-- Mobile Development -->
                <div class="product-card" id="product-mobile">
                    <div class="product-icon">📱</div>
                    <h3>Mobile App Development</h3>
                    <p class="product-description">
                        Custom mobile applications for iOS and Android with cutting-edge technology and seamless user experiences.
                    </p>
                    <ul class="feature-list">
                        <li>Native iOS & Android Development</li>
                        <li>Cross-Platform Solutions (React Native, Flutter)</li>
                        <li>UI/UX Design & Prototyping</li>
                        <li>App Store Optimization</li>
                        <li>Push Notifications & Analytics</li>
                        <li>Third-party API Integration</li>
                        <li>Maintenance & Support</li>
                    </ul>
                    <div class="product-footer">
                        <button class="product-btn" onclick="openModal('demoModal')">Learn More</button>
                    </div>
                </div>

                <!-- Security Testing -->
                <div class="product-card" id="product-security">
                    <div class="product-icon">🔒</div>
                    <h3>Security Testing Services</h3>
                    <p class="product-description">
                        Comprehensive cybersecurity testing and assessment services to protect your digital assets and ensure compliance.
                    </p>
                    <ul class="feature-list">
                        <li>Penetration Testing</li>
                        <li>Vulnerability Assessments</li>
                        <li>Security Code Reviews</li>
                        <li>Compliance Auditing (SOC 2, GDPR)</li>
                        <li>Network Security Testing</li>
                        <li>Web Application Security</li>
                        <li>Risk Assessment & Mitigation</li>
                    </ul>
                    <div class="product-footer">
                        <button class="product-btn" onclick="openModal('demoModal')">Learn More</button>
                    </div>
                </div>

                <!-- IT Infrastructure -->
                <div class="product-card" id="product-infrastructure">
                    <div class="product-icon">🏗️</div>
                    <h3>IT Infrastructure Services</h3>
                    <p class="product-description">
                        Complete IT infrastructure solutions including cloud architecture, server management, and scalable infrastructure design.
                    </p>
                    <ul class="feature-list">
                        <li>Cloud Architecture & Migration</li>
                        <li>Server Management & Monitoring</li>
                        <li>Network Design & Implementation</li>
                        <li>DevOps & CI/CD Pipelines</li>
                        <li>Infrastructure as Code</li>
                        <li>Disaster Recovery Planning</li>
                        <li>Performance Optimization</li>
                    </ul>
                    <div class="product-footer">
                        <button class="product-btn" onclick="openModal('demoModal')">Learn More</button>
                    </div>
                </div>

                <!-- AI Development -->
                <div class="product-card" id="product-ai">
                    <div class="product-icon">🤖</div>
                    <h3>AI Development & RPA Services</h3>
                    <p class="product-description">
                        Custom artificial intelligence and Robotic Process Automation solutions including machine learning models, intelligent automation, and end-to-end process optimization.
                    </p>
                    <ul class="feature-list">
                        <li>Machine Learning Model Development</li>
                        <li>Robotic Process Automation (RPA)</li>
                        <li>Natural Language Processing (NLP)</li>
                        <li>Computer Vision Solutions</li>
                        <li>Intelligent Document Processing</li>
                        <li>Business Process Automation</li>
                        <li>AI-Powered Chatbots & Virtual Assistants</li>
                        <li>Workflow Automation & Optimization</li>
                        <li>Predictive Analytics Models</li>
                        <li>Recommendation Systems</li>
                    </ul>
                    <div class="product-footer">
                        <button class="product-btn" onclick="openModal('demoModal')">Learn More</button>
                    </div>
                </div>

                <!-- Data Science -->
                <div class="product-card" id="product-datascience">
                    <div class="product-icon">🔬</div>
                    <h3>Data Science Solutions</h3>
                    <p class="product-description">
                        Advanced data science services including statistical analysis, data modeling, and research-driven insights for strategic decision making.
                    </p>
                    <ul class="feature-list">
                        <li>Statistical Analysis & Modeling</li>
                        <li>Predictive Analytics</li>
                        <li>Data Mining & Pattern Recognition</li>
                        <li>A/B Testing & Experimentation</li>
                        <li>Advanced Forecasting Models</li>
                        <li>Risk Analysis & Assessment</li>
                        <li>Research & Development Support</li>
                    </ul>
                    <div class="product-footer">
                        <button class="product-btn" onclick="openModal('demoModal')">Learn More</button>
                    </div>
                </div>

                <!-- Data Analytics -->
                <div class="product-card" id="product-analytics">
                    <div class="product-icon">📈</div>
                    <h3>Data Analytics Platform</h3>
                    <p class="product-description">
                        Comprehensive business intelligence and data analytics solutions with real-time dashboards and actionable insights.
                    </p>
                    <ul class="feature-list">
                        <li>Business Intelligence Dashboards</li>
                        <li>Real-time Data Visualization</li>
                        <li>KPI Monitoring & Reporting</li>
                        <li>Interactive Analytics Tools</li>
                        <li>Data Warehouse Solutions</li>
                        <li>ETL Pipeline Development</li>
                        <li>Custom Reporting Systems</li>
                    </ul>
                    <div class="product-footer">
                        <button class="product-btn" onclick="openModal('demoModal')">Learn More</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="team">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Leadership Team</div>
                <h2 class="heading-lg">Meet Our Executives</h2>
                <p class="text-lg">
                    Experienced leaders driving innovation and excellence in enterprise technology solutions
                </p>
            </div>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-photo">👨‍💼</div>
                    <h3 class="member-name">David Chen</h3>
                    <p class="member-role">Chief Executive Officer</p>
                    <p class="member-bio">
                        15+ years leading enterprise software companies. Former VP at Microsoft Azure, driving digital transformation for Fortune 500 companies.
                    </p>
                </div>
                
                <div class="team-member">
                    <div class="member-photo">👩‍💻</div>
                    <h3 class="member-name">Sarah Williams</h3>
                    <p class="member-role">Chief Technology Officer</p>
                    <p class="member-bio">
                        Technology visionary with 12+ years architecting scalable systems. Former Principal Engineer at Google Cloud, expert in AI and machine learning.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Client Feedback</div>
                <h2 class="heading-lg">What Our Clients Say</h2>
                <p class="text-lg">
                    Trusted by leading enterprises across various industries worldwide
                </p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p class="testimonial-content">
                        "CoreTech's CRM platform transformed our sales process completely. We've seen a 40% increase in lead conversion and our team productivity has improved dramatically. The support team is exceptional."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">JM</div>
                        <div class="author-info">
                            <h4>James Miller</h4>
                            <p>VP Sales, TechCorp Industries</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <p class="testimonial-content">
                        "The LMS solution helped us train over 5,000 employees globally. The analytics and reporting features give us incredible insights into learning progress and effectiveness."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">AL</div>
                        <div class="author-info">
                            <h4>Amanda Lee</h4>
                            <p>Chief Learning Officer, Global Dynamics</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <p class="testimonial-content">
                        "CoreTech's security testing identified critical vulnerabilities we didn't know existed. Their thorough approach and detailed reporting helped us achieve SOC 2 compliance ahead of schedule."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">RK</div>
                        <div class="author-info">
                            <h4>Robert Kim</h4>
                            <p>CISO, Financial Services Inc.</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <p class="testimonial-content">
                        "The mobile app they developed for us has over 100K downloads and 4.8-star rating. The development process was smooth and they delivered ahead of deadline."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">MR</div>
                        <div class="author-info">
                            <h4>Maria Rodriguez</h4>
                            <p>Product Manager, RetailTech Solutions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="section-header">
                <div class="section-badge">Get In Touch</div>
                <h2 class="heading-lg">Contact Our Team</h2>
                <p class="text-lg">
                    Ready to transform your business? Let's discuss your requirements
                </p>
            </div>
            
            <div class="contact-content">
                <div class="contact-info">
                    <h3>Contact Information</h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">📍</div>
                        <div class="contact-details">
                            <h4>Headquarters</h4>
                            <p>123 Tech Boulevard, Suite 500<br>San Francisco, CA 94105</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">📞</div>
                        <div class="contact-details">
                            <h4>Phone</h4>
                            <p>+1 (555) 123-4567<br>+1 (555) 123-4568</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">✉️</div>
                        <div class="contact-details">
                            <h4>Email</h4>
                            <p>sales@coretech.com<br>support@coretech.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">🕒</div>
                        <div class="contact-details">
                            <h4>Business Hours</h4>
                            <p>Monday - Friday: 9:00 AM - 6:00 PM PST<br>24/7 Enterprise Support Available</p>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <form>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-input" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Message</label>
                            <textarea class="form-textarea" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2 class="heading-lg">Ready to Transform Your Business?</h2>
            <p>
                Join leading enterprises who trust CoreTech to drive their digital transformation. 
                Start your journey with a personalized demo and see the difference our solutions can make.
            </p>
            <div class="hero-buttons">
                <button class="btn btn-primary btn-lg" onclick="openModal('demoModal')">Schedule Demo</button>
                <a href="#contact" class="btn btn-secondary btn-lg">Contact Sales</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <a href="#" class="logo">
                        <div class="logo-icon">C</div>
                        <span class="logo-text">CoreTech</span>
                    </a>
                    <p class="footer-description">
                        Empowering businesses with innovative technology solutions. 
                        Built for enterprise scale, designed for human success.
                    </p>
                </div>
                <div class="footer-section">
                    <h3>Products</h3>
                    <ul class="footer-links">
                        <li><a href="#products" onclick="scrollToProducts('crm')">CRM Platform</a></li>
                        <li><a href="#products" onclick="scrollToProducts('lms')">LMS Solution</a></li>
                        <li><a href="#products" onclick="scrollToProducts('mobile')">Mobile Development</a></li>
                        <li><a href="#products" onclick="scrollToProducts('security')">Security Testing</a></li>
                        <li><a href="#products" onclick="scrollToProducts('infrastructure')">IT Infrastructure</a></li>
                        <li><a href="#products" onclick="scrollToProducts('ai')">AI Development & RPA</a></li>
                        <li><a href="#products" onclick="scrollToProducts('datascience')">Data Science</a></li>
                        <li><a href="#products" onclick="scrollToProducts('analytics')">Data Analytics</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Company</h3>
                    <ul class="footer-links">
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#careers">Careers</a></li>
                        <li><a href="#news">News & Press</a></li>
                        <li><a href="#team">Leadership Team</a></li>
                        <li><a href="#">Investors</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="#support">Help Center</a></li>
                        <li><a href="#support">Documentation</a></li>
                        <li><a href="#contact">Contact Support</a></li>
                        <li><a href="#locations">Office Locations</a></li>
                        <li><a href="#support">System Status</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 CoreTech Solutions. All rights reserved. Enterprise software solutions for modern businesses.</p>
            </div>
        </div>
    </footer>

    <script>
        function openModal(modalId) {
            event.preventDefault();
            event.stopPropagation();
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            event.preventDefault();
            event.stopPropagation();
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) {
                    activeModal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            }
        });

        // Prevent form submission from closing modal
        document.addEventListener('submit', function(event) {
            const modal = event.target.closest('.modal');
            if (modal) {
                event.preventDefault();
                alert('Demo request submitted successfully! Our team will contact you within 24 hours.');
                closeModal('demoModal');
            }
        });
    </script>
</body>
</html>