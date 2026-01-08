<?php
include "config/db.php";

// Create contacts table if it does not exist
$conn->query("CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $message = trim($_POST["message"] ?? "");
    $ip = $_SERVER['REMOTE_ADDR'];

    if ($name === "" || $email === "" || $message === "") {
        header("Location: index.php?error=empty#contact");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?error=email#contact");
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO contact_messages (name, email, message, ip_address)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $name, $email, $message, $ip);
    $stmt->execute();

    header("Location: index.php?success=1#contact");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>KhanhDev | Portfolio</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
/* CSS Reset & Variables */
:root {
    /* Light Theme */
    --primary: #6366F1;
    --primary-hover: #4F46E5;
    --text-main: #0F172A;
    --text-muted: #64748B;
    --bg-main: rgba(255, 255, 255, 0.75); /* Nền trắng mờ giúp chữ dễ đọc hơn */
    --bg-alt: rgba(255, 255, 255, 0.6);
    --bg-card: rgba(255, 255, 255, 0.8);
    --bg-input: rgba(255, 255, 255, 0.9);
    --bg-header: rgba(255, 255, 255, 0.8);
    --border-color: rgba(255, 255, 255, 0.6);
    --white: #FFFFFF;
}

[data-theme="dark"] {
    --primary: #818CF8;
    --primary-hover: #6366F1;
    --text-main: #E2E8F0;
    --text-muted: #CBD5E1;
    --bg-main: rgba(15, 23, 42, 0.65); /* Nền tối mờ giúp chữ dễ đọc hơn */
    --bg-alt: rgba(30, 41, 59, 0.4);
    --bg-card: rgba(30, 41, 59, 0.6);
    --bg-input: rgba(15, 23, 42, 0.6);
    --bg-header: rgba(15, 23, 42, 0.7);
    --border-color: rgba(148, 163, 184, 0.2);
}

* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Inter', sans-serif; color: var(--text-main); background: var(--bg-main); line-height: 1.6; transition: background-color 0.3s, color 0.3s; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
a { text-decoration: none; color: inherit; transition: 0.3s; }
ul { list-style: none; }
img { max-width: 100%; display: block; }
html { scroll-behavior: smooth; }

/* Layout */
.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.section { padding: 80px 0; }
.bg-white { background: transparent; } /* Loại bỏ nền xen kẽ để thấy vũ trụ liên tục */
.text-center { text-align: center; }

/* Header */
header { background: var(--bg-header); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 1000; border-bottom: 1px solid var(--border-color); }
.header-content { display: flex; justify-content: space-between; align-items: center; height: 70px; }
.logo { font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; gap: 10px; cursor: pointer; }
.logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), #8B5CF6); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.logo span { color: var(--primary); }
.nav-menu { display: flex; gap: 30px; align-items: center; }
.nav-menu a { font-weight: 500; color: var(--text-muted); font-size: 0.95rem; }
.nav-menu a:hover { color: var(--primary); }
.btn-contact { background: var(--primary); color: white; padding: 10px 24px; border-radius: 8px; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2); }
.btn-contact:hover { background: var(--primary-hover); transform: translateY(-2px); }
.mobile-menu-btn { display: none; background: none; border: none; cursor: pointer; color: var(--text-main); }
.theme-toggle-btn { background: var(--bg-card); border: 1px solid var(--border-color); cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 50%; position: relative; overflow: hidden; transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
.theme-toggle-btn:hover { border-color: var(--primary); color: var(--primary); box-shadow: 0 5px 15px rgba(79, 70, 229, 0.15); transform: rotate(15deg); }
.theme-toggle-btn .material-symbols-outlined { position: absolute; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.theme-toggle-btn .sun-icon { opacity: 1; transform: rotate(0) scale(1); }
.theme-toggle-btn .moon-icon { opacity: 0; transform: rotate(90deg) scale(0); }
[data-theme="dark"] .theme-toggle-btn .sun-icon { opacity: 0; transform: rotate(-90deg) scale(0); }
[data-theme="dark"] .theme-toggle-btn .moon-icon { opacity: 1; transform: rotate(0) scale(1); }
.nav-controls { display: flex; align-items: center; gap: 10px; }

/* Hero */
.hero { display: flex; align-items: center; gap: 60px; padding: 100px 0; min-height: 80vh; }
.hero-text { flex: 1; }
.hero-img { flex: 1; display: flex; justify-content: center; position: relative; }
.hero-img img { border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.15); width: 100%; max-width: 450px; position: relative; z-index: 2; }
.hero-badge { display: inline-flex; align-items: center; gap: 8px; background: var(--bg-card); border: 1px solid var(--border-color); padding: 6px 16px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
.status-dot { width: 10px; height: 10px; background: #22C55E; border-radius: 50%; position: relative; }
.status-dot::after { content: ''; position: absolute; inset: -4px; border-radius: 50%; background: #22C55E; opacity: 0.3; animation: ping 1.5s infinite; }
@keyframes ping { 0% { transform: scale(1); opacity: 0.5; } 100% { transform: scale(2); opacity: 0; } }

h1 { font-size: 3.5rem; font-weight: 900; line-height: 1.2; margin-bottom: 20px; color: var(--text-main); }
h1 span { background: linear-gradient(to right, var(--primary), #8B5CF6); -webkit-background-clip: text; color: transparent; }
.hero-subtitle { font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin-bottom: 20px; opacity: 0.9; }
.hero-desc { font-size: 1.1rem; color: var(--text-muted); margin-bottom: 30px; max-width: 550px; }

.contact-info { margin-top: 30px; border-left: 4px solid rgba(79, 70, 229, 0.3); padding-left: 25px; display: flex; flex-direction: column; gap: 12px; }
.contact-row { display: flex; align-items: center; gap: 12px; color: var(--text-muted); font-weight: 500; }
.contact-row .material-symbols-outlined { color: var(--primary); font-size: 20px; background: var(--bg-card); padding: 6px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

.hero-btns { margin-top: 40px; display: flex; gap: 15px; }
.btn { display: inline-flex; align-items: center; justify-content: center; height: 52px; padding: 0 32px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 1rem; transition: 0.3s; }
.btn-primary { background: var(--primary); color: white; box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3); }
.btn-primary:hover { background: var(--primary-hover); transform: translateY(-3px); box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4); }
.btn-outline { background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-main); }
.btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--bg-main); }

/* Section Titles */
.section-header { margin-bottom: 60px; }
.section-title { font-size: 2.5rem; font-weight: 900; margin-bottom: 15px; color: var(--text-main); }
.section-desc { color: var(--text-muted); font-size: 1.1rem; max-width: 700px; }
.divider { height: 6px; width: 80px; background: linear-gradient(to right, var(--primary), #8B5CF6); border-radius: 3px; margin-top: 15px; }

/* About */
.about-container { display: flex; gap: 60px; align-items: flex-start; }
.about-text { flex: 3; }
.about-features { flex: 2; display: grid; gap: 20px; }
.feature-card { background: var(--bg-card); backdrop-filter: blur(12px); padding: 25px; border-radius: 16px; border: 1px solid var(--border-color); transition: 0.3s; }
.feature-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-color: var(--primary); }
.feature-icon { width: 48px; height: 48px; background: rgba(79, 70, 229, 0.1); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; }
.feature-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 5px; }
.feature-desc { font-size: 0.9rem; color: var(--text-muted); }

/* Skills */
.skills-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px; }
.skill-card { background: var(--bg-card); backdrop-filter: blur(12px); border: 1px solid var(--border-color); padding: 25px 15px; border-radius: 16px; display: flex; flex-direction: column; align-items: center; gap: 15px; transition: 0.3s; cursor: default; }
.skill-card:hover { border-color: var(--primary); transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
.skill-icon-box { width: 50px; height: 50px; background: var(--bg-main); border-radius: 12px; display: flex; align-items: center; justify-content: center; padding: 8px; }
.skill-icon { width: 100%; height: 100%; object-fit: contain; }
.skill-name { font-weight: 700; font-size: 0.95rem; color: var(--text-main); }

/* Projects */
.projects-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 50px; }
.link-more { color: var(--primary); font-weight: 700; display: flex; align-items: center; gap: 5px; }
.link-more:hover { text-decoration: underline; }

.projects-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; }
.project-card { background: var(--bg-card); backdrop-filter: blur(12px); border-radius: 20px; overflow: hidden; border: 1px solid var(--border-color); transition: 0.3s; display: flex; flex-direction: column; }
.project-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); border-color: rgba(79, 70, 229, 0.3); }
.project-img { height: 220px; background-size: cover; background-position: center; position: relative; background-color: #e2e8f0; }
.project-overlay { position: absolute; inset: 0; background: rgba(79, 70, 229, 0.85); opacity: 0; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 15px; backdrop-filter: blur(2px); }
.project-card:hover .project-overlay { opacity: 1; }
.icon-btn { background: white; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); transition: 0.2s; }
.icon-btn:hover { transform: scale(1.1); }

.project-info { padding: 25px; flex: 1; display: flex; flex-direction: column; }
.project-title { font-size: 1.3rem; font-weight: 800; margin-bottom: 10px; color: var(--text-main); }
.project-desc { font-size: 0.95rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.project-tags { margin-top: auto; display: flex; gap: 8px; flex-wrap: wrap; padding-top: 20px; border-top: 1px dashed var(--border-color); }
.tag { background: rgba(79, 70, 229, 0.08); color: var(--primary); padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }

/* Experience/Education */
.timeline-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; }
.timeline-header { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; }
.timeline-icon { width: 48px; height: 48px; background: var(--bg-card); backdrop-filter: blur(12px); border: 1px solid var(--border-color); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.timeline-item { position: relative; padding-left: 35px; border-left: 2px solid var(--border-color); padding-bottom: 40px; }
.timeline-item:last-child { border-left-color: transparent; padding-bottom: 0; }
.timeline-dot { position: absolute; left: -9px; top: 0; width: 16px; height: 16px; background: var(--bg-main); border-radius: 50%; border: 4px solid var(--bg-alt); box-shadow: 0 0 0 2px var(--border-color); transition: 0.3s; }
.timeline-item:hover .timeline-dot { background: var(--primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2); }

.timeline-content { position: relative; top: -5px; }
.timeline-date { display: inline-block; background: var(--text-main); color: var(--bg-alt); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
.timeline-date.highlight { background: var(--primary); }
.timeline-role { font-size: 1.1rem; font-weight: 800; margin-bottom: 5px; color: var(--text-main); }
.timeline-company { font-weight: 600; color: var(--text-muted); margin-bottom: 10px; font-size: 0.9rem; }
.timeline-desc { font-size: 0.95rem; color: var(--text-muted); }

/* Certificates */
.certificates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 25px;
}
.certificate-card {
    background: var(--bg-card);
    backdrop-filter: blur(12px);
    border: 1px solid var(--border-color);
    padding: 25px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: 0.3s;
}
.certificate-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    border-color: var(--primary);
}
.certificate-icon {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}
.certificate-icon .material-symbols-outlined {
    font-size: 36px;
    color: white;
}
.certificate-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 5px; }
.certificate-issuer { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 10px; }
.certificate-link { font-size: 0.9rem; font-weight: 700; color: var(--primary); display: inline-flex; align-items: center; gap: 5px; }
.certificate-link:hover { text-decoration: underline; }

/* Contact */
.contact-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 60px; }
.contact-info-box { display: flex; flex-direction: column; gap: 30px; }
.contact-card { display: flex; align-items: center; gap: 20px; padding: 20px; background: var(--bg-card); backdrop-filter: blur(12px); border-radius: 16px; border: 1px solid var(--border-color); transition: 0.3s; }
.contact-card:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: var(--primary); }
.contact-card-icon { width: 50px; height: 50px; background: var(--bg-alt); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary); border: 1px solid var(--border-color); font-size: 24px; }
.contact-card-title { font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-bottom: 2px; }
.contact-card-value { color: var(--text-muted); font-size: 0.95rem; }

.social-links { display: flex; gap: 15px; margin-top: 20px; }
.social-btn { width: 50px; height: 50px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-muted); transition: 0.3s; }
.social-btn:hover { background: var(--primary); color: white; border-color: var(--primary); transform: translateY(-3px); box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3); }

.form-box { background: var(--bg-card); backdrop-filter: blur(12px); padding: 40px; border-radius: 24px; border: 1px solid var(--border-color); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
.form-group { margin-bottom: 20px; }
.form-label { display: block; font-weight: 700; margin-bottom: 8px; font-size: 0.9rem; color: var(--text-main); margin-left: 5px; }
.form-input { width: 100%; padding: 14px 18px; border: 1px solid var(--border-color); border-radius: 10px; font-family: inherit; transition: 0.3s; background: var(--bg-input); font-size: 1rem; color: var(--text-main); }
.form-input:focus { outline: none; border-color: var(--primary); background: var(--bg-alt); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
.form-input::placeholder { color: #94A3B8; }

.alert { padding: 16px; border-radius: 10px; margin-bottom: 25px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
.alert-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
.alert-error { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }

/* Footer */
footer { background: transparent; border-top: 1px solid var(--border-color); padding: 40px 0; text-align: center; color: var(--text-muted); font-weight: 500; font-size: 0.9rem; backdrop-filter: blur(5px); }

/* Responsive */
@media (max-width: 992px) {
    .hero { flex-direction: column-reverse; text-align: center; padding: 60px 0; gap: 40px; }
    .hero-img { width: 100%; }
    .contact-info { align-items: center; border-left: none; border-top: 1px solid var(--border-light); padding-left: 0; padding-top: 20px; width: 100%; }
    .hero-btns { justify-content: center; }
    .about-container { flex-direction: column; }
    .timeline-grid, .contact-grid { grid-template-columns: 1fr; }
    h1 { font-size: 2.8rem; }
    .projects-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .nav-menu { display: none; }
    .mobile-menu-btn { display: block; }
    .header-content { height: 60px; }
    h1 { font-size: 2.2rem; }
    .section-title { font-size: 2rem; }
    .form-box { padding: 25px; }
}

/* Scroll Reveal Animation */
.reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
.reveal.active { opacity: 1; transform: translateY(0); }

/* Back to Top Button */
.back-to-top { position: fixed; bottom: 30px; right: 30px; width: 50px; height: 50px; background: var(--primary); color: white; border: none; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3); z-index: 999; opacity: 0; visibility: hidden; transform: translateY(20px); transition: all 0.3s ease; }
.back-to-top.show { opacity: 1; visibility: visible; transform: translateY(0); }
.back-to-top:hover { background: var(--primary-hover); transform: translateY(-5px); box-shadow: 0 8px 15px rgba(79, 70, 229, 0.4); }
@media (max-width: 768px) {
    .back-to-top { bottom: 20px; right: 20px; width: 45px; height: 45px; }
}

/* Universe Background */
#universe-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    background: #000;
}
</style>
</head>
<body>

<header>
    <div class="container header-content">
        <div class="logo">
            <div class="logo-icon">
                <span class="material-symbols-outlined" style="color: white; font-size: 20px;">code</span>
            </div>
            <div>Khanh<span>Dev</span></div>
        </div>
        <div class="nav-controls">
            <nav class="nav-menu">
                <a href="#home">Home</a>
                <a href="#about">About</a>
                <a href="#skills">Skills</a>
                <a href="#projects">Projects</a>
                <a href="#experience">Experience</a>
                <a href="#certificates">Chứng chỉ</a>
            </nav>
            <button id="theme-toggle" class="theme-toggle-btn" title="Toggle theme">
                <span class="material-symbols-outlined sun-icon">light_mode</span>
                <span class="material-symbols-outlined moon-icon">dark_mode</span>
            </button>
            <a href="#contact" class="btn-contact">Liên hệ</a>
            <button class="mobile-menu-btn">
                <span class="material-symbols-outlined">menu</span>
            </button>
        </div>
    </div>
</header>

<main>
    <section id="home" class="section">
        <div class="container hero">
            <div class="hero-text reveal">
                <div class="hero-badge">
                    <div class="status-dot"></div>
                    Available for work
                </div>
                <h1>Xin chào, tôi là <br><span>Khánh Trần</span> 👋</h1>
                <h2 class="hero-subtitle">💻 Web Developer / Sinh viên CNTT</h2>
                <p class="hero-desc">
                    Tôi là một người đam mê lập trình web, yêu thích việc xây dựng các website thân thiện với người dùng, giao diện đẹp và hoạt động ổn định.
                </p>
                
                <div class="contact-info">
                    <div class="contact-row">
                        <span class="material-symbols-outlined">location_on</span>
                        Việt Nam
                    </div>
                    <div class="contact-row">
                        <span class="material-symbols-outlined">mail</span>
                        <a href="mailto:khanhtran@example.com">tranvankhanh2k4@gmail.com</a>
                    </div>
                    <div class="contact-row">
                        <span class="material-symbols-outlined">call</span>
                        <a href="tel:0705364798">070 5364 798</a>
                    </div>
                </div>

                <div class="hero-btns">
                    <a href="#projects" class="btn btn-primary">Xem Dự Án</a>
                    <a href="#" class="btn btn-outline">
                        <span class="material-symbols-outlined" style="margin-right: 8px;">download</span>
                        Tải CV
                    </a>
                </div>
            </div>
            <div class="hero-img reveal">
                <img src="5.jpg" alt="Khanh Tran">
            </div>
        </div>
    </section>

    <section id="about" class="section bg-white">
        <div class="container">
            <div class="about-container">
                <div class="about-text">
                    <div class="section-header reveal">
                        <h2 class="section-title">Giới thiệu</h2>
                        <div class="divider"></div>
                    </div>
                    <div class="section-desc">
                        <p style="margin-bottom: 20px;">
                            Tôi hiện đang là sinh viên ngành Công Nghệ Thông Tin, có định hướng phát triển trong lĩnh vực lập trình web. Trong quá trình học tập, tôi đã thực hiện nhiều project thực tế như Website trắc nghiệm, Hệ thống lưu điểm, và Portfolio cá nhân.
                        </p>
                        <p>
                            Tôi có khả năng tự học tốt, luôn tìm hiểu công nghệ mới và sẵn sàng tiếp thu góp ý để cải thiện sản phẩm. Mục tiêu của tôi là trở thành Web Developer chuyên nghiệp và tham gia các dự án thực tế có giá trị.
                        </p>
                    </div>
                </div>
                <div class="about-features">
                    <div class="feature-card reveal">
                        <div class="feature-icon">
                            <span class="material-symbols-outlined">terminal</span>
                        </div>
                        <h3 class="feature-title">Clean Code</h3>
                        <p class="feature-desc">Mã nguồn sạch, dễ bảo trì và mở rộng.</p>
                    </div>
                    <div class="feature-card reveal">
                        <div class="feature-icon">
                            <span class="material-symbols-outlined">devices</span>
                        </div>
                        <h3 class="feature-title">Responsive</h3>
                        <p class="feature-desc">Giao diện tương thích mọi thiết bị.</p>
                    </div>
                    <div class="feature-card reveal">
                        <div class="feature-icon">
                            <span class="material-symbols-outlined">rocket_launch</span>
                        </div>
                        <h3 class="feature-title">Hiệu năng</h3>
                        <p class="feature-desc">Tối ưu hóa tốc độ và trải nghiệm người dùng.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="skills" class="section">
        <div class="container">
            <div class="section-header reveal">
                <h2 class="section-title">Kỹ năng chuyên môn</h2>
                <p class="section-desc">Danh sách các công cụ, ngôn ngữ và framework tôi sử dụng.</p>
                <div class="divider"></div>
            </div>
            
            <h3 style="margin-bottom: 20px; color: var(--primary); text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px;">Ngôn ngữ & Công nghệ</h3>
            <div class="skills-grid">
                <div class="skill-card reveal">
                    <div class="skill-icon-box">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDPeflfKTwuXQrV1soUB8GXcyE2Kj7tKeHdjuDjsAzq7U5i0my49xxzA1ErRzy9JOcw8fP7RFPK9MSk-ZAplw7Nd3-H8sge0CNBRU7aXFOCIFDPAE4Yg7eO9N8qBBDIv_haQ0dni82H9klWTpIxEJMvOiDMYA4DVXoGtc9B_Rm4aGyGxyqerwhTc-71VsZJk1cgcMGirszJOR2owG29cWfECVhGps5o-L07oXRFEPX9WKPn_FTbmI-R6SRp_4jLIxjIzCvxpDxwwiE" class="skill-icon" alt="HTML5">
                    </div>
                    <span class="skill-name">HTML5</span>
                </div>
                <div class="skill-card reveal">
                    <div class="skill-icon-box">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuC3EgTOLx8Qj8yggZQYwj5dRAbuHPyWMapV-wNly2eAt_IIK0htDwlnxjgLk2X_CGb08f5tadhE2KtnBgVNVHtvmbjvcmQIfGHPP3_JYR9Y4-jt2Ty-JbxJlWVKEuN-k71GCAsJUyVmDN5XskA1xmDeUeSv8maK1mS_ked5WJWIFvbpTCbZDDF8D2GR8Kz0Wm2DUJ_XqEs0VIiXHrhIwCQdffvxKsegvRFM92NfTdQXf6ynuCrKOIGJ_I086gHl7D8ay7XZnE98M-c" class="skill-icon" alt="CSS3">
                    </div>
                    <span class="skill-name">CSS3</span>
                </div>
                <div class="skill-card reveal">
                    <div class="skill-icon-box">
                        <span class="material-symbols-outlined" style="color: #EAB308; font-size: 32px;">javascript</span>
                    </div>
                    <span class="skill-name">JavaScript</span>
                </div>
                <div class="skill-card reveal">
                    <div class="skill-icon-box">
                        <span class="material-symbols-outlined" style="color: #4F46E5; font-size: 32px;">php</span>
                    </div>
                    <span class="skill-name">PHP</span>
                </div>
                <div class="skill-card reveal">
                    <div class="skill-icon-box">
                        <span class="material-symbols-outlined" style="color: #2563EB; font-size: 32px;">database</span>
                    </div>
                    <span class="skill-name">MySQL</span>
                </div>
                <div class="skill-card reveal">
                    <div class="skill-icon-box">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuA7YoYSs9MaQza_maw-6_berHGFcu_fLbWby5NjzYwE-jnv4BWsqY5GqVvYB9_QPek3xWz1wyuhjp0FAi7kEpZDJOh1Dp4gGc4UYfwBglyzjTJpMsMR0_0oxUKZmVEjnzyNow4b2YpsZS8W35gXUeiiuX2FWa48ekFQnwVzSANdQVdJTRtYEtdPtU4uWRXTaG5F6NrIOy9lxHE00MFy20AQvKD2UKFuxxaiHcFEFDuvzMMtMsXScd7E8oCik7VqfQD2CrOf2YpfqJQ" class="skill-icon" alt="Node.js">
                    </div>
                    <span class="skill-name">Node.js</span>
                </div>
                <div class="skill-card reveal">
                    <div class="skill-icon-box">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDltVU0sTt8RrypHSWuomEqw7rYE7Q6Ov-86Frbac0rQ-HC9qblx7gi0buci4KhU7cx4ifrMFu0nr2XItVWQfisXblYM18hjxIbEHARY7fJ8LHR4gocfP7fuk5itv5lxNUY6nP3tFvkxYPg30NVz25rH9GmRLVr3FOKnw49Oi2SvaZu8NvqFvdayjEMmA2EgHecwYF8JKeNJlpbMymk8h1Kfdgt7W_QGEy77ky_uRvqtYqm6Jp9MaIzjJdrRUJWowb8xiPckYK_Eh8" class="skill-icon" alt="Git">
                    </div>
                    <span class="skill-name">Git</span>
                </div>
            </div>
        </div>
    </section>

    <section id="projects" class="section bg-white">
        <div class="container">
            <div class="projects-header reveal">
                <div>
                    <h2 class="section-title">Dự án tiêu biểu</h2>
                    <p class="section-desc">Một số dự án tôi đã thực hiện để rèn luyện kỹ năng.</p>
                    <div class="divider"></div>
                </div>
                <a href="#" class="link-more">
                    Xem Github <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>

            <div class="projects-grid">
                <div class="project-card reveal">
                    <div class="project-img" style="background-image: url('9.jpg');">
                        <div class="project-overlay">
                            <a href="https://khanhedu8909.dpdns.org/voice-quiz/" class="icon-btn" target="_blank" title="Xem Demo"><span class="material-symbols-outlined">visibility</span></a>
                            <a href="#" class="icon-btn"><span class="material-symbols-outlined">code</span></a>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3 class="project-title">Website Trắc Nghiệm</h3>
                        <p class="project-desc">Hệ thống thi trắc nghiệm trực tuyến cho học sinh. Tính năng bao gồm: làm bài thi, tính điểm tự động, lưu kết quả và bảng xếp hạng.</p>
                        <div class="project-tags">
                            <span class="tag">PHP</span>
                            <span class="tag">MySQL</span>
                            <span class="tag">HTML/CSS</span>
                        </div>
                    </div>
                </div>

                <div class="project-card reveal">
                    <div class="project-img" style="background-image: url('6.jpg');">
                        <div class="project-overlay">
                            <a href="https://khanhedu8909.dpdns.org/pofolio/" class="icon-btn" target="_blank" title="Xem Demo"><span class="material-symbols-outlined">visibility</span></a>
                            <a href="#" class="icon-btn"><span class="material-symbols-outlined">code</span></a>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3 class="project-title">Portfolio Cá Nhân</h3>
                        <p class="project-desc">Website giới thiệu bản thân với giao diện hiện đại, responsive và dễ dàng tùy chỉnh nội dung.</p>
                        <div class="project-tags">
                            <span class="tag">CSS</span>
                            <span class="tag">PHP</span>
                        </div>
                    </div>
                </div>

                <div class="project-card reveal">
                    <div class="project-img" style="background-image: url('8.jpg');">
                        <div class="project-overlay">
                            <a href="https://shoeshop.dpdns.org/shoe_shop/shoe_shop_project/" class="icon-btn" target="_blank" title="Xem Demo"><span class="material-symbols-outlined">visibility</span></a>
                            <a href="#" class="icon-btn"><span class="material-symbols-outlined">code</span></a>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3 class="project-title">Shop Bán Hàng</h3>
                        <p class="project-desc">Website thương mại điện tử đầy đủ tính năng: giỏ hàng, thanh toán, quản lý đơn hàng và admin dashboard thống kê doanh thu.</p>
                        <div class="project-tags">
                            <span class="tag">Laravel</span>
                            <span class="tag">MySQL</span>
                            <span class="tag">Bootstrap</span>
                        </div>
                    </div>
                </div>

                <div class="project-card reveal">
                    <div class="project-img" style="background-image: url('https://images.unsplash.com/photo-1540350394557-8d14678e7f91?w=800&q=80');">
                        <div class="project-overlay">
                            <a href="#" class="icon-btn" target="_blank" title="Xem Demo"><span class="material-symbols-outlined">visibility</span></a>
                            <a href="#" class="icon-btn"><span class="material-symbols-outlined">code</span></a>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3 class="project-title">App Quản Lý Công Việc</h3>
                        <p class="project-desc">Ứng dụng Todo List hỗ trợ kéo thả (Drag & Drop), phân loại công việc theo mức độ ưu tiên và nhắc nhở deadline.</p>
                        <div class="project-tags">
                            <span class="tag">ReactJS</span>
                            <span class="tag">Node.js</span>
                            <span class="tag">MongoDB</span>
                        </div>
                    </div>
                </div>

                <div class="project-card reveal">
                    <div class="project-img" style="background-image: url('7.jpg');">
                        <div class="project-overlay">   
                            <a href="https://khanhedu8909.dpdns.org/edu_web/" class="icon-btn" target="_blank" title="Xem Demo"><span class="material-symbols-outlined">visibility</span></a>
                            <a href="#" class="icon-btn"><span class="material-symbols-outlined">code</span></a>
                        </div>
                    </div>
                    <div class="project-info">
                        <h3 class="project-title">Blog Chia Sẻ Kiến Thức</h3>
                        <p class="project-desc">Nền tảng blog cá nhân tích hợp CMS tự xây dựng, hỗ trợ soạn thảo Markdown, comment và tối ưu SEO.</p>
                        <div class="project-tags">
                            <span class="tag">PHP MVC</span>
                            <span class="tag">jQuery</span>
                            <span class="tag">SASS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="experience" class="section">
        <div class="container">
            <div class="timeline-grid">
                <div>
                    <div class="timeline-header">
                        <div class="timeline-icon">
                            <span class="material-symbols-outlined">school</span>
                        </div>
                        <h2 class="section-title" style="margin:0; font-size: 2rem;">Học vấn</h2>
                    </div>
                    <div class="timeline-item reveal">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <span class="timeline-date highlight">202x - Nay</span>
                            <h3 class="timeline-role">Công Nghệ Thông Tin</h3>
                            <div class="timeline-company">Trường Đại Học (Tên trường)</div>
                            <p class="timeline-desc">Chuyên ngành Kỹ thuật phần mềm. Đang tập trung nghiên cứu về Web Development và Database.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="timeline-header">
                        <div class="timeline-icon">
                            <span class="material-symbols-outlined">work</span>
                        </div>
                        <h2 class="section-title" style="margin:0; font-size: 2rem;">Kinh nghiệm</h2>
                    </div>
                    <div class="timeline-item reveal">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <span class="timeline-date">202x - Nay</span>
                            <h3 class="timeline-role">Làm Project Cá Nhân</h3>
                            <div class="timeline-company">Tự học & Nghiên cứu</div>
                            <p class="timeline-desc">Thực hành code mỗi ngày, xây dựng các dự án nhỏ để rèn luyện kỹ năng. Có kinh nghiệm làm báo cáo đồ án và làm việc nhóm.</p>
                        </div>
                    </div>
                    <div class="timeline-item reveal">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <span class="timeline-date">2023</span>
                            <h3 class="timeline-role">Phát triển Website Trắc Nghiệm</h3>
                            <div class="timeline-company">Dự án PHP & MySQL</div>
                            <p class="timeline-desc">Xây dựng hệ thống thi trực tuyến: quản lý ngân hàng câu hỏi, tạo đề thi tự động, chấm điểm realtime và xếp hạng kết quả.</p>
                        </div>
                    </div>
                    <div class="timeline-item reveal">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <span class="timeline-date">2023</span>
                            <h3 class="timeline-role">Xây dựng Blog Chia Sẻ</h3>
                            <div class="timeline-company">Dự án PHP MVC</div>
                            <p class="timeline-desc">Phát triển nền tảng chia sẻ kiến thức học tập. Tự xây dựng mô hình MVC, routing và hệ thống quản trị nội dung (CMS) để đăng tải bài viết.</p>
                        </div>
                    </div>
                    <div class="timeline-item reveal">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <span class="timeline-date">2022</span>
                            <h3 class="timeline-role">Lập trình Website Bán Hàng</h3>
                            <div class="timeline-company">Dự án E-commerce (PHP)</div>
                            <p class="timeline-desc">Thực hiện website bán hàng đầy đủ tính năng: Giỏ hàng, Thanh toán, Quản lý đơn hàng và Thống kê doanh thu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="certificates" class="section bg-white">
        <div class="container">
            <div class="section-header text-center reveal" style="margin-bottom: 50px;">
                <h2 class="section-title">Chứng chỉ & Thành tích</h2>
                <p class="section-desc" style="margin: 0 auto;">Các chứng chỉ tôi đã đạt được trong quá trình học tập.</p>
                <div class="divider" style="margin: 15px auto;"></div>
            </div>
            <div class="certificates-grid">
                <div class="certificate-card reveal">
                    <div class="certificate-icon" style="background: #0078D4;">
                        <span class="material-symbols-outlined">workspace_premium</span>
                    </div>
                    <div>
                        <h3 class="certificate-title">Microsoft Office Specialist</h3>
                        <p class="certificate-issuer">Cấp bởi: Microsoft</p>
                        <a href="#" class="certificate-link">
                            Xem chi tiết <span class="material-symbols-outlined" style="font-size: 1.1rem;">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <div class="certificate-card reveal">
                    <div class="certificate-icon" style="background: #F9A825;">
                        <span class="material-symbols-outlined">translate</span>
                    </div>
                    <div>
                        <h3 class="certificate-title">TOEIC 750+</h3>
                        <p class="certificate-issuer">Cấp bởi: ETS (Educational Testing Service)</p>
                        <a href="#" class="certificate-link">
                            Xem chi tiết <span class="material-symbols-outlined" style="font-size: 1.1rem;">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="section bg-white">
        <div class="container">
            <div class="text-center reveal" style="margin-bottom: 50px;">
                <h2 class="section-title">Liên hệ</h2>
                <p class="section-desc" style="margin: 0 auto;">Bạn có dự án thú vị hoặc muốn hợp tác? Hãy liên hệ với tôi.</p>
                <div class="divider" style="margin: 15px auto;"></div>
            </div>

            <div class="contact-grid reveal">
                <div class="contact-info-box">
                    <div class="contact-card">
                        <div class="contact-card-icon">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div>
                            <h3 class="contact-card-title">Email</h3>
                            <p class="contact-card-value">khanhtran@example.com</p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="contact-card-icon">
                            <span class="material-symbols-outlined">call</span>
                        </div>
                        <div>
                            <h3 class="contact-card-title">Điện thoại</h3>
                            <p class="contact-card-value"><a href="tel:0705364798">070 5364 798</a></p>
                        </div>
                    </div>
                    <div class="contact-card">
                        <div class="contact-card-icon">
                            <span class="material-symbols-outlined">location_on</span>
                        </div>
                        <div>
                            <h3 class="contact-card-title">Địa chỉ</h3>
                            <p class="contact-card-value">TP. Hồ Chí Minh, Việt Nam</p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <h3 style="font-weight: 700; margin-bottom: 15px;">Kết nối với tôi</h3>
                        <div class="social-links">
                            <a href="https://facebook.com" target="_blank" class="social-btn" title="Facebook"><span class="material-symbols-outlined">public</span></a>
                            <a href="https://github.com" target="_blank" class="social-btn" title="Github"><span class="material-symbols-outlined">code</span></a>
                            <a href="mailto:tranvankhanh2k4@gmail.com" class="social-btn" title="Email"><span class="material-symbols-outlined">mail</span></a>
                        </div>
                    </div>

                    <!-- Google Map Embed -->
                    <div style="margin-top: 20px; border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.424167939069!2d106.68777307480492!3d10.77878628937021!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f2a66e6370f%3A0x286f2a16b2538d2b!2zRGluaCDEkOG7mWMgTOG6rXA!5e0!3m2!1svi!2s!4v1709222222222!5m2!1svi!2s" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

                <div class="form-box">
                    <form method="POST" action="">
                        <?php if (isset($_GET['success'])): ?>
                          <div class="alert alert-success">
                            <span class="material-symbols-outlined">check_circle</span>
                            Gửi liên hệ thành công!
                          </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['error']) && $_GET['error'] === 'empty'): ?>
                          <div class="alert alert-error">
                            <span class="material-symbols-outlined">error</span>
                            Vui lòng nhập đầy đủ thông tin!
                          </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['error']) && $_GET['error'] === 'email'): ?>
                          <div class="alert alert-error">
                            <span class="material-symbols-outlined">error</span>
                            Email không hợp lệ!
                          </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="form-label" for="name">Họ tên</label>
                            <input name="name" class="form-input" id="name" placeholder="Nhập tên của bạn" type="text"/>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input name="email" class="form-input" id="email" placeholder="email@example.com" type="email"/>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="message">Tin nhắn</label>
                            <textarea name="message" class="form-input" id="message" placeholder="Nội dung tin nhắn..." rows="4"></textarea>
                        </div>
                        <button class="btn btn-primary" style="width: 100%;" type="submit">
                            Gửi Tin Nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <p>© 2023 Khanh Tran Portfolio. All rights reserved.</p>
    </div>
</footer>

<button id="back-to-top" class="back-to-top" title="Back to Top">
    <span class="material-symbols-outlined">arrow_upward</span>
</button>

<script>
    const themeBtn = document.getElementById('theme-toggle');
    const html = document.documentElement;
    
    // Check saved theme
    if (localStorage.getItem('theme') === 'dark') {
        html.setAttribute('data-theme', 'dark');
    }

    themeBtn.addEventListener('click', () => {
        if (html.getAttribute('data-theme') === 'dark') {
            html.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
        } else {
            html.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
    });

    // Scroll Reveal Animation
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.reveal').forEach(el => {
        observer.observe(el);
    });

    // Back to Top Logic
    const backToTopBtn = document.getElementById('back-to-top');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });

    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>

<!-- THREE.JS Universe Background -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://unpkg.com/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script src="https://unpkg.com/three@0.128.0/examples/js/postprocessing/EffectComposer.js"></script>
<script src="https://unpkg.com/three@0.128.0/examples/js/postprocessing/RenderPass.js"></script>
<script src="https://unpkg.com/three@0.128.0/examples/js/postprocessing/ShaderPass.js"></script>
<script src="https://unpkg.com/three@0.128.0/examples/js/shaders/CopyShader.js"></script>
<script src="https://unpkg.com/three@0.128.0/examples/js/shaders/LuminosityHighPassShader.js"></script>
<script src="https://unpkg.com/three@0.128.0/examples/js/postprocessing/UnrealBloomPass.js"></script>

<script>
    // Setup Scene
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 2000);
    camera.position.set(0, 20, 80);

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.domElement.id = 'universe-background';
    document.body.appendChild(renderer.domElement);

    // Controls
    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.enableZoom = false; // Disable zoom to not interfere with scroll
    controls.autoRotate = true;
    controls.autoRotateSpeed = 0.8;

    // Lights
    scene.add(new THREE.AmbientLight(0xffffff, 0.5));
    const sunLight = new THREE.PointLight(0xffee88, 2.5, 500);
    sunLight.position.set(0, 0, 0);
    scene.add(sunLight);

    // Sun
    const sun = new THREE.Mesh(
        new THREE.SphereGeometry(6, 32, 32),
        new THREE.MeshBasicMaterial({ color: 0xffcc33 })
    );
    scene.add(sun);

    // Planets
    const planets = [];
    function createPlanet(size, distance, speed, color, ring) {
        const mesh = new THREE.Mesh(
            new THREE.SphereGeometry(size, 32, 32),
            new THREE.MeshStandardMaterial({ color })
        );
        if (ring) {
            const ringGeo = new THREE.RingGeometry(ring.inner, ring.outer, 32);
            const ringMat = new THREE.MeshBasicMaterial({
                color: ring.color, side: THREE.DoubleSide, transparent: true, opacity: 0.4
            });
            const ringMesh = new THREE.Mesh(ringGeo, ringMat);
            ringMesh.rotation.x = -Math.PI / 2;
            mesh.add(ringMesh);
        }
        scene.add(mesh);
        planets.push({ mesh, distance, angle: Math.random() * Math.PI * 2, speed });
    }
    createPlanet(2, 15, 0.01, 0x3399ff);
    createPlanet(3, 25, 0.006, 0xff5533);
    createPlanet(2.5, 35, 0.004, 0x66ff66);
    createPlanet(4, 50, 0.002, 0xaa66ff, { inner: 5, outer: 8, color: 0xffccff });

    // Galaxy
    const galaxyGeometry = new THREE.BufferGeometry();
    const positions = [];
    for (let i = 0; i < 12000; i++) {
        const radius = Math.random() * 500;
        const angle = radius * 0.04;
        positions.push(Math.cos(angle) * radius, (Math.random() - 0.5) * 20, Math.sin(angle) * radius);
    }
    galaxyGeometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
    const galaxy = new THREE.Points(galaxyGeometry, new THREE.PointsMaterial({ color: 0xffffff, size: 1, transparent: true }));
    scene.add(galaxy);

    // Nebula
    function createCloudTexture() {
        const canvas = document.createElement('canvas');
        canvas.width = 32; canvas.height = 32;
        const ctx = canvas.getContext('2d');
        const grad = ctx.createRadialGradient(16, 16, 0, 16, 16, 16);
        grad.addColorStop(0, 'rgba(255, 255, 255, 1)');
        grad.addColorStop(1, 'rgba(0, 0, 0, 0)');
        ctx.fillStyle = grad; ctx.fillRect(0, 0, 32, 32);
        const texture = new THREE.Texture(canvas);
        texture.needsUpdate = true;
        return texture;
    }
    const nebulaGeo = new THREE.BufferGeometry();
    const nPos = [], nCol = [];
    for (let i = 0; i < 500; i++) {
        const r = 200 + Math.random() * 600;
        const theta = Math.random() * Math.PI * 2;
        const phi = Math.random() * Math.PI;
        nPos.push(r * Math.sin(phi) * Math.cos(theta), r * Math.sin(phi) * Math.sin(theta), r * Math.cos(phi));
        const color = new THREE.Color();
        color.setHSL(0.6 + Math.random() * 0.3, 0.8, 0.4);
        nCol.push(color.r, color.g, color.b);
    }
    nebulaGeo.setAttribute('position', new THREE.Float32BufferAttribute(nPos, 3));
    nebulaGeo.setAttribute('color', new THREE.Float32BufferAttribute(nCol, 3));
    const nebula = new THREE.Points(nebulaGeo, new THREE.PointsMaterial({
        size: 80, map: createCloudTexture(), vertexColors: true, transparent: true, opacity: 0.15, depthWrite: false, blending: THREE.AdditiveBlending
    }));
    scene.add(nebula);

    // Stars
    function createStars(count, size) {
        const geo = new THREE.BufferGeometry();
        const pos = [];
        for (let i = 0; i < count; i++) {
            const r = 600 + Math.random() * 1000;
            const theta = Math.random() * Math.PI * 2;
            const phi = Math.acos(2 * Math.random() - 1);
            pos.push(r * Math.sin(phi) * Math.cos(theta), r * Math.sin(phi) * Math.sin(theta), r * Math.cos(phi));
        }
        geo.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
        const mat = new THREE.PointsMaterial({ color: 0xffffff, size: size * 1.5, transparent: true });
        const mesh = new THREE.Points(geo, mat);
        scene.add(mesh);
        return { mesh, material: mat };
    }
    const stars1 = createStars(1500, 1.5);
    const stars2 = createStars(500, 2.5);

    // Post-processing
    const renderScene = new THREE.RenderPass(scene, camera);
    const bloomPass = new THREE.UnrealBloomPass(new THREE.Vector2(window.innerWidth, window.innerHeight), 1.5, 0.4, 0.85);
    bloomPass.strength = 2.5; bloomPass.radius = 0.4; bloomPass.threshold = 0;
    const composer = new THREE.EffectComposer(renderer);
    composer.addPass(renderScene);
    composer.addPass(bloomPass);

    // Animation
    let mouseX = 0, mouseY = 0;
    document.addEventListener("mousemove", e => {
        mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
        mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
    });

    function animate() {
        requestAnimationFrame(animate);
        const time = Date.now() * 0.001;
        stars1.material.opacity = 0.5 + Math.sin(time * 0.5) * 0.4;
        stars2.material.opacity = 0.5 + Math.cos(time * 0.8) * 0.4;
        stars1.mesh.rotation.y += 0.0001;
        stars2.mesh.rotation.y += 0.0002;
        sun.rotation.y += 0.003;
        planets.forEach(p => {
            p.angle += p.speed;
            p.mesh.position.set(Math.cos(p.angle) * p.distance, 0, Math.sin(p.angle) * p.distance);
            p.mesh.rotation.y += 0.01;
        });
        galaxy.rotation.y += 0.0008;
        nebula.rotation.y -= 0.0005;
        camera.position.x += mouseX * 0.5;
        camera.position.y += -mouseY * 0.5;
        controls.update();
        composer.render();
    }
    animate();
    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
        composer.setSize(window.innerWidth, window.innerHeight);
    });
</script>
</body>
</html>
