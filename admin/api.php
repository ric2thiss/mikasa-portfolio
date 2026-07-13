<?php
session_start();
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Public actions (no login needed)
$publicActions = ['submit_contact'];

// Require login for admin actions
if (!in_array($action, $publicActions) && !isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    switch ($action) {

        /* ========== ANALYTICS ========== */
        case 'get_analytics_data':
            try {
                $filter = $_GET['filter'] ?? 'week';
                
                // Time filter logic
                $dateCondition = "";
                if ($filter == 'day') $dateCondition = "visit_date = CURDATE()";
                elseif ($filter == 'week') $dateCondition = "visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                elseif ($filter == 'month') $dateCondition = "visit_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                elseif ($filter == 'year') $dateCondition = "visit_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                else $dateCondition = "1=1";

                // Visits Line Chart Data
                $visitsQuery = $pdo->prepare("SELECT visit_date, COUNT(*) as count FROM page_views WHERE $dateCondition GROUP BY visit_date ORDER BY visit_date ASC");
                $visitsQuery->execute();
                $visitsData = $visitsQuery->fetchAll();

                // Location Bar Chart Data
                $locQuery = $pdo->prepare("SELECT country, COUNT(*) as count FROM page_views WHERE $dateCondition GROUP BY country ORDER BY count DESC LIMIT 10");
                $locQuery->execute();
                $locData = $locQuery->fetchAll();

                echo json_encode(['success' => true, 'visits' => $visitsData, 'locations' => $locData]);
            } catch (PDOException $e) {
                // If table doesn't exist yet, just return empty data so charts still render blank
                echo json_encode(['success' => true, 'visits' => [], 'locations' => []]);
            }
            break;

        /* ========== PAGES ========== */
        case 'get_pages':
            $rows = $pdo->query("SELECT * FROM pages ORDER BY created_at ASC")->fetchAll();
            echo json_encode(['success' => true, 'data' => $rows]);
            break;
            
        case 'add_page':
            $title = $_POST['title'] ?? '';
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($title)));
            $slug = trim($slug, '-');
            
            // Generate unique slug
            $check = $pdo->prepare("SELECT id FROM pages WHERE slug=?");
            $check->execute([$slug]);
            if ($check->fetch()) {
                $slug .= '-' . time();
            }
            
            $stmt = $pdo->prepare("INSERT INTO pages (title, slug) VALUES (?, ?)");
            $stmt->execute([$title, $slug]);
            echo json_encode(['success' => true, 'page_id' => $pdo->lastInsertId()]);
            break;
            
        case 'delete_page':
            $pdo->prepare("DELETE FROM pages WHERE id=?")->execute([$_POST['id']]);
            echo json_encode(['success' => true]);
            break;

        /* ========== BUILDER ========== */
        case 'get_sections':
            $page_id = $_GET['page_id'] ?? 1;
            $stmt = $pdo->prepare("SELECT * FROM sections WHERE page_id=? ORDER BY sort_order");
            $stmt->execute([$page_id]);
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        case 'save_section_order':
            $order = json_decode($_POST['order'] ?? '[]', true);
            foreach ($order as $item) {
                $pdo->prepare("UPDATE sections SET sort_order=? WHERE id=?")->execute([$item['sort_order'], $item['id']]);
            }
            echo json_encode(['success' => true]);
            break;

        case 'toggle_section_vis':
            $pdo->prepare("UPDATE sections SET is_visible=? WHERE id=?")->execute([$_POST['state'], $_POST['id']]);
            echo json_encode(['success' => true]);
            break;

        case 'add_custom_section':
            $title = $_POST['title'];
            $page_id = $_POST['page_id'] ?? 1;
            $sort = $pdo->prepare("SELECT MAX(sort_order) FROM sections WHERE page_id=?");
            $sort->execute([$page_id]);
            $maxSort = $sort->fetchColumn() + 1;
            $pdo->prepare("INSERT INTO sections (page_id, section_type, title, content, sort_order) VALUES (?, 'richtext', ?, '{}', ?)")->execute([$page_id, $title, $maxSort]);
            echo json_encode(['success' => true]);
            break;
            
        case 'save_custom_section':
            $id = $_POST['id'];
            $html = $_POST['html'];
            $pdo->prepare("UPDATE sections SET content=? WHERE id=?")->execute([json_encode(['html' => $html]), $id]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_section':
            $pdo->prepare("DELETE FROM sections WHERE id=?")->execute([$_POST['id']]);
            echo json_encode(['success' => true]);
            break;

        /* ========== SETTINGS ========== */
        case 'get_settings':
            echo json_encode(['success' => true, 'data' => getAllSettings($pdo)]);
            break;

        case 'update_settings':
            $fields = json_decode($_POST['fields'] ?? '{}', true);
            if ($fields) {
                foreach ($fields as $key => $value) {
                    updateSetting($pdo, $key, $value);
                }
            }
            echo json_encode(['success' => true]);
            break;

        /* ========== STATS ========== */
        case 'get_stats':
            $rows = $pdo->query("SELECT * FROM stats ORDER BY sort_order")->fetchAll();
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'save_stat':
            $id = $_POST['id'] ?? null;
            $num = $_POST['stat_num'] ?? '';
            $label = $_POST['stat_label'] ?? '';
            $order = $_POST['sort_order'] ?? 0;
            if ($id) {
                $stmt = $pdo->prepare("UPDATE stats SET stat_num=?, stat_label=?, sort_order=? WHERE id=?");
                $stmt->execute([$num, $label, $order, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO stats (stat_num, stat_label, sort_order) VALUES (?,?,?)");
                $stmt->execute([$num, $label, $order]);
            }
            echo json_encode(['success' => true]);
            break;

        case 'delete_stat':
            $id = $_POST['id'] ?? 0;
            $pdo->prepare("DELETE FROM stats WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        /* ========== SERVICES ========== */
        case 'get_services':
            $rows = $pdo->query("SELECT * FROM services ORDER BY sort_order")->fetchAll();
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'save_service':
            $id = $_POST['id'] ?? null;
            $num = $_POST['num'] ?? '';
            $name = $_POST['name'] ?? '';
            $desc = $_POST['description'] ?? '';
            $order = $_POST['sort_order'] ?? 0;
            if ($id) {
                $stmt = $pdo->prepare("UPDATE services SET num=?, name=?, description=?, sort_order=? WHERE id=?");
                $stmt->execute([$num, $name, $desc, $order, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO services (num, name, description, sort_order) VALUES (?,?,?,?)");
                $stmt->execute([$num, $name, $desc, $order]);
            }
            echo json_encode(['success' => true]);
            break;

        case 'delete_service':
            $id = $_POST['id'] ?? 0;
            $pdo->prepare("DELETE FROM services WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        /* ========== PORTFOLIO ========== */
        case 'get_portfolio':
            $rows = $pdo->query("SELECT * FROM portfolio ORDER BY sort_order")->fetchAll();
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'save_portfolio':
            $id = $_POST['id'] ?? null;
            $catName = $_POST['category_name'] ?? '';
            $catTag = $_POST['category_tag'] ?? '';
            $order = $_POST['sort_order'] ?? 0;
            $displayType = $_POST['display_type'] ?? 'carousel';
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($catName)));
            $slug = trim($slug, '-');

            if ($id) {
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../assets/images/portfolio/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = 'portfolio_' . time() . '_' . uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
                    $imagePath = 'assets/images/portfolio/' . $filename;
                    
                    $stmt = $pdo->prepare("UPDATE portfolio SET category_name=?, category_tag=?, slug=?, display_type=?, image_path=?, sort_order=? WHERE id=?");
                    $stmt->execute([$catName, $catTag, $slug, $displayType, $imagePath, $order, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE portfolio SET category_name=?, category_tag=?, slug=?, display_type=?, sort_order=? WHERE id=?");
                    $stmt->execute([$catName, $catTag, $slug, $displayType, $order, $id]);
                }
            } else {
                $imagePath = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = __DIR__ . '/../assets/images/portfolio/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = 'portfolio_' . time() . '_' . uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
                    $imagePath = 'assets/images/portfolio/' . $filename;
                }
                $stmt = $pdo->prepare("INSERT INTO portfolio (category_name, category_tag, slug, display_type, image_path, sort_order) VALUES (?,?,?,?,?,?)");
                $stmt->execute([$catName, $catTag, $slug, $displayType, $imagePath, $order]);
            }
            echo json_encode(['success' => true]);
            break;

        case 'delete_portfolio':
            $id = $_POST['id'] ?? 0;
            $pdo->prepare("DELETE FROM portfolio WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        /* ========== GALLERY IMAGES ========== */
        case 'get_gallery_images':
            $portfolioId = $_GET['portfolio_id'] ?? 0;
            $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE portfolio_id=? ORDER BY sort_order");
            $stmt->execute([$portfolioId]);
            $rows = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'add_gallery_image':
            $portfolioId = $_POST['portfolio_id'] ?? 0;
            $caption = $_POST['caption'] ?? '';
            
            $sort = $pdo->prepare("SELECT COALESCE(MAX(sort_order), 0) FROM gallery_images WHERE portfolio_id=?");
            $sort->execute([$portfolioId]);
            $maxSort = $sort->fetchColumn();

            $uploaded = 0;
            $errors = [];
            $uploadDir = __DIR__ . '/../assets/images/portfolio/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            // Check if post_max_size was exceeded (causes empty $_POST and $_FILES)
            if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
                echo json_encode(['success' => false, 'error' => 'Total file size exceeds server limits. Please upload fewer or smaller images.']);
                exit;
            }

            // Handle multiple files
            if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                $count = count($_FILES['images']['name']);
                for ($i = 0; $i < $count; $i++) {
                    $errCode = $_FILES['images']['error'][$i];
                    if ($errCode === UPLOAD_ERR_OK) {
                        $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                        $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
                        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $uploadDir . $filename)) {
                            $imagePath = 'assets/images/portfolio/' . $filename;
                            $maxSort++;
                            $stmt = $pdo->prepare("INSERT INTO gallery_images (portfolio_id, image_path, caption, sort_order) VALUES (?,?,?,?)");
                            $stmt->execute([$portfolioId, $imagePath, $caption, $maxSort]);
                            $uploaded++;
                        } else {
                            $errors[] = "Failed to save file: " . $_FILES['images']['name'][$i];
                        }
                    } elseif ($errCode !== UPLOAD_ERR_NO_FILE) {
                        if ($errCode == UPLOAD_ERR_INI_SIZE) $errors[] = $_FILES['images']['name'][$i] . " exceeds maximum upload size.";
                        else $errors[] = "Error code $errCode for " . $_FILES['images']['name'][$i];
                    }
                }
            } 
            // Fallback for single file upload just in case
            elseif (isset($_FILES['image'])) {
                $errCode = $_FILES['image']['error'];
                if ($errCode === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                        $imagePath = 'assets/images/portfolio/' . $filename;
                        $maxSort++;
                        $stmt = $pdo->prepare("INSERT INTO gallery_images (portfolio_id, image_path, caption, sort_order) VALUES (?,?,?,?)");
                        $stmt->execute([$portfolioId, $imagePath, $caption, $maxSort]);
                        $uploaded++;
                    } else {
                        $errors[] = "Failed to save the file.";
                    }
                } elseif ($errCode !== UPLOAD_ERR_NO_FILE) {
                    if ($errCode == UPLOAD_ERR_INI_SIZE) $errors[] = "File exceeds maximum upload size.";
                    else $errors[] = "Upload error code: $errCode";
                }
            }

            if ($uploaded > 0) {
                echo json_encode(['success' => true, 'errors' => $errors]);
            } else {
                $errMsg = count($errors) > 0 ? implode(', ', $errors) : 'No valid images were uploaded.';
                echo json_encode(['success' => false, 'error' => $errMsg]);
            }
            break;

        case 'delete_gallery_image':
            $id = $_POST['id'] ?? 0;
            $stmt = $pdo->prepare("SELECT image_path FROM gallery_images WHERE id=?");
            $stmt->execute([$id]);
            $img = $stmt->fetchColumn();
            if ($img && file_exists(__DIR__ . '/../' . $img)) {
                @unlink(__DIR__ . '/../' . $img);
            }
            $pdo->prepare("DELETE FROM gallery_images WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        case 'save_gallery_order':
            $order = json_decode($_POST['order'] ?? '[]', true);
            foreach ($order as $item) {
                $pdo->prepare("UPDATE gallery_images SET sort_order=? WHERE id=?")->execute([$item['sort_order'], $item['id']]);
            }
            echo json_encode(['success' => true]);
            break;

        /* ========== SOCIAL LINKS ========== */
        case 'get_socials':
            $rows = $pdo->query("SELECT * FROM social_links ORDER BY sort_order")->fetchAll();
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'save_social':
            $id = $_POST['id'] ?? null;
            $platform = $_POST['platform_name'] ?? '';
            $url = $_POST['url'] ?? '';
            $order = $_POST['sort_order'] ?? 0;
            if ($id) {
                $stmt = $pdo->prepare("UPDATE social_links SET platform_name=?, url=?, sort_order=? WHERE id=?");
                $stmt->execute([$platform, $url, $order, $id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO social_links (platform_name, url, sort_order) VALUES (?,?,?)");
                $stmt->execute([$platform, $url, $order]);
            }
            echo json_encode(['success' => true]);
            break;

        case 'delete_social':
            $id = $_POST['id'] ?? 0;
            $pdo->prepare("DELETE FROM social_links WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        /* ========== ABOUT IMAGE UPLOAD ========== */
        case 'upload_about_image':
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../assets/images/';
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'about_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
                $path = 'assets/images/' . $filename;
                updateSetting($pdo, 'about_image', $path);
                echo json_encode(['success' => true, 'path' => $path]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            }
            break;

        /* ========== CONTACT MESSAGES (public submit) ========== */
        case 'submit_contact':
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)");
            $stmt->execute([$name, $email, $subject, $message]);
            echo json_encode(['success' => true]);
            break;

        case 'get_messages':
            $rows = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        case 'delete_message':
            $id = $_POST['id'] ?? 0;
            $pdo->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        case 'mark_read':
            $id = $_POST['id'] ?? 0;
            $pdo->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
