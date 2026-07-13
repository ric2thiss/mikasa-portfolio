CREATE DATABASE IF NOT EXISTS mikasa_portfolio;
USE mikasa_portfolio;

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    num VARCHAR(10),
    name VARCHAR(100),
    description VARCHAR(255),
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100),
    category_tag VARCHAR(100),
    image_path VARCHAR(255),
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stat_num VARCHAR(50),
    stat_label VARCHAR(100),
    sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS social_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform_name VARCHAR(50),
    url VARCHAR(255),
    sort_order INT DEFAULT 0
);

/* Insert default settings based on the static HTML */
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('hero_ghost_1', 'MIKASA'),
('hero_ghost_2', 'PHOTOGRAPHY'),
('hero_eyebrow', 'Freelance Photographer — Dubai'),
('hero_name_1', 'Mikasa Fine Arts'),
('hero_name_2', 'Photography'),
('hero_tags', 'Fine Arts,Fashion,Portraits,Graphic Design'),
('about_label', 'About'),
('about_heading', 'Capturing the <em>Ethereal</em> — One Frame at a Time'),
('about_desc_1', 'I am Mikasa, a freelance Fine Arts Photographer based in the vibrant city of Dubai. My work merges high fashion with evocative, painterly compositions — creating visual narratives that resonate with depth and emotion.'),
('about_desc_2', 'Specializing in Fashion Portraits, Weddings, Corporate events, and conceptual storytelling, every frame is meticulously composed to reflect beauty in its most raw and poetic form.'),
('about_quote', 'Photography is not about capturing a moment — it''s about revealing the art that already exists within it.'),
('about_image', 'assets/images/gallery-portrait-1.png'),
('services_title', 'What I Do'),
('services_desc', 'A curated range of visual services, from editorial fashion to immersive event coverage.'),
('portfolio_title', 'Selected Works'),
('portfolio_desc', 'Explore by Category'),
('testimonial_label', 'Client Words'),
('testimonial_quote', 'Mikasa doesn''t just take photographs — she creates experiences. Every image feels like stepping into a gallery where light and emotion are perfectly curated.'),
('testimonial_author', '— Sarah Al Maktoum, Dubai'),
('cta_ghost', 'CONTACT'),
('cta_heading', 'Let''s Create<br>Something Beautiful'),
('cta_desc', 'Available for freelance projects nationwide. Currently based in Dubai, UAE.'),
('cta_email', 'hello@mikasafineart.com'),
('footer_brand', 'Mikasa Fine Arts'),
('footer_copy', '© 2026 All Rights Reserved');

/* Insert default stats */
INSERT IGNORE INTO stats (stat_num, stat_label, sort_order) VALUES
('8+', 'Years Exp.', 1),
('350+', 'Projects', 2),
('12', 'Countries', 3);

/* Insert default services */
INSERT IGNORE INTO services (num, name, description, sort_order) VALUES
('01', 'Fine Arts Photography', 'Conceptual & Painterly', 1),
('02', 'Fashion & Editorial', 'Lookbooks & Campaigns', 2),
('03', 'Portrait Sessions', 'Studio & On-Location', 3),
('04', 'Wedding Stories', 'Cinematic & Timeless', 4),
('05', 'Graphic Design', 'Branding & Visual Identity', 5);

/* Insert default portfolio */
INSERT IGNORE INTO portfolio (category_name, category_tag, image_path, sort_order) VALUES
('Events', 'Corporate & Social Gatherings', 'assets/images/gallery-fashion-1.png', 1),
('Corporates', 'Professional & Branding', 'assets/images/gallery-art-1.png', 2),
('Portraits', 'Fine Art & Studio', 'assets/images/gallery-portrait-1.png', 3),
('Ecommerce', 'Product & Catalog', 'assets/images/gallery-fashion-2.png', 4),
('Weddings', 'Cinematic & Timeless', 'assets/images/gallery-portrait-2.png', 5),
('Fashion', 'Editorial & Haute Couture', 'assets/images/hero-portrait.png', 6),
('Travel', 'Destinations & Culture', 'assets/images/gallery-art-1.png', 7);

/* Insert default social links */
INSERT IGNORE INTO social_links (platform_name, url, sort_order) VALUES
('Instagram', '#', 1),
('Behance', '#', 2),
('LinkedIn', '#', 3);
