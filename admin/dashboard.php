<?php 
$currentPage = 'dashboard';
include 'includes/header.php'; 

// Fetch image counts
$portfolioCounts = $pdo->query("
    SELECT p.category_name, COUNT(g.id) as image_count 
    FROM portfolio p 
    LEFT JOIN gallery_images g ON p.id = g.portfolio_id 
    GROUP BY p.id
")->fetchAll();

// Get disk space
function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
}
$freeSpace = @disk_free_space(__DIR__);
$totalSpace = @disk_total_space(__DIR__);
$usedSpace = $totalSpace - $freeSpace;
$spaceText = $freeSpace !== false ? formatBytes($freeSpace) . " Free" : "Unknown";
$usedText = $totalSpace !== false ? formatBytes($usedSpace) . " Used of " . formatBytes($totalSpace) : "Unknown";
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.analytics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem; }
.chart-card { background: var(--dark-2); border: 1px solid var(--gray-light); padding: 1.5rem; border-radius: 8px; }
.stats-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 2rem; }
.stat-card { background: var(--dark-2); border: 1px solid var(--gray-light); padding: 1.5rem; border-radius: 8px; text-align: center; }
.stat-card h3 { font-size: 2rem; margin: 0 0 0.5rem 0; color: #50c878; }
.stat-card p { margin: 0; color: var(--gray); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; }
.filter-select { background: var(--dark-3); color: var(--light); border: 1px solid var(--gray-light); padding: 0.5rem; border-radius: 4px; outline: none; }
@media (max-width: 900px) { .analytics-grid { grid-template-columns: 1fr; } }
</style>

<div class="admin-card" style="border:none; background:transparent; padding:0;">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>Website Analytics</h2>
        <select id="time-filter" class="filter-select" onchange="loadAnalytics()">
            <option value="week">Last 7 Days</option>
            <option value="month">Last Month</option>
            <option value="year">Last Year</option>
            <option value="all">All Time</option>
        </select>
    </div>

    <div class="analytics-grid">
        <div class="chart-card">
            <h3 style="margin-top:0;">Index Page Visits</h3>
            <canvas id="visitsChart"></canvas>
        </div>
        <div class="chart-card">
            <h3 style="margin-top:0;">Visitor Locations</h3>
            <canvas id="locationsChart"></canvas>
        </div>
    </div>

    <h3 style="margin-top: 3rem; margin-bottom: 1rem;">Recent Visitor Log</h3>
    <div style="overflow-x:auto; background:var(--dark-2); border:1px solid var(--gray-light); border-radius:8px;">
        <table style="width:100%; border-collapse:collapse; text-align:left; font-size:0.9rem;">
            <thead style="background:var(--dark-3); border-bottom:1px solid var(--gray-light);">
                <tr>
                    <th style="padding:1rem;">IP Address</th>
                    <th style="padding:1rem;">Country</th>
                    <th style="padding:1rem;">City</th>
                    <th style="padding:1rem;">Date</th>
                    <th style="padding:1rem;">Time</th>
                    <th style="padding:1rem;">Device / Browser</th>
                </tr>
            </thead>
            <tbody id="raw-data-body">
                <tr><td colspan="6" style="padding:1rem; text-align:center; color:var(--gray);">Loading data...</td></tr>
            </tbody>
        </table>
    </div>

    <h2 style="margin-top: 3rem;">Portfolio Storage Stats</h2>
    <div class="stats-cards">
        <div class="stat-card" style="border-left: 4px solid #3498db;">
            <h3><?= $usedText ?></h3>
            <p><?= $spaceText ?></p>
            <div style="margin-top:0.5rem; font-size:0.7rem; color:var(--gray-light)">Disk Storage Overview</div>
        </div>
        
        <?php foreach($portfolioCounts as $pc): ?>
        <div class="stat-card">
            <h3><?= number_format($pc['image_count']) ?></h3>
            <p><?= htmlspecialchars($pc['category_name']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
let visitsChartObj = null;
let locChartObj = null;

async function loadAnalytics() {
    const filter = document.getElementById('time-filter').value;
    const res = await fetch(`api.php?action=get_analytics_data&filter=${filter}`);
    const json = await res.json();
    if (!json.success) return;

    // Prepare Visits Data
    const visitLabels = json.visits.map(v => v.visit_date);
    const visitData = json.visits.map(v => parseInt(v.count));

    // Prepare Location Data
    const locLabels = json.locations.map(v => v.country || 'Unknown');
    const locData = json.locations.map(v => parseInt(v.count));

    Chart.defaults.color = '#cccccc';

    // Visits Chart
    const ctxVisits = document.getElementById('visitsChart').getContext('2d');
    if (visitsChartObj) visitsChartObj.destroy();
    visitsChartObj = new Chart(ctxVisits, {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#50c878',
                backgroundColor: 'rgba(80, 200, 120, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Locations Chart
    const ctxLocs = document.getElementById('locationsChart').getContext('2d');
    if (locChartObj) locChartObj.destroy();
    locChartObj = new Chart(ctxLocs, {
        type: 'bar',
        data: {
            labels: locLabels,
            datasets: [{
                label: 'Visitors',
                data: locData,
                backgroundColor: '#3498db',
                borderRadius: 4
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Populate Raw Data Table
    const tbody = document.getElementById('raw-data-body');
    if (!json.raw_data || json.raw_data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="padding:1rem; text-align:center; color:var(--gray);">No visitors found for this period.</td></tr>';
    } else {
        tbody.innerHTML = json.raw_data.map(row => {
            const timeStr = row.visit_time ? row.visit_time.split(' ')[1] : '';
            return `<tr style="border-bottom:1px solid var(--dark-3);">
                <td style="padding:1rem;">${row.ip_address || ''}</td>
                <td style="padding:1rem;">${row.country || ''}</td>
                <td style="padding:1rem;">${row.city || ''}</td>
                <td style="padding:1rem;">${row.visit_date || ''}</td>
                <td style="padding:1rem;">${timeStr}</td>
                <td style="padding:1rem; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${row.user_agent || ''}">${row.user_agent || 'Unknown'}</td>
            </tr>`;
        }).join('');
    }
}

document.addEventListener('DOMContentLoaded', loadAnalytics);
</script>

<?php include 'includes/footer.php'; ?>