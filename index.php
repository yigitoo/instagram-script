<?php

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://www.instagram.com/' . $_GET['username'] . '/embed/',
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Linux; Android 6.0.1; SM-G935S Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36',
    CURLOPT_RETURNTRANSFER => true
]);
$output = curl_exec($ch);
curl_close($ch);

$photo = '';
$username = $_GET['username'];
$followers = 0;
$postCount = 0;
$posts = [];

$regex = '@\\\"owner\\\":{\\\"id\\\":\\\"([0-9]+)\\\",\\\"profile_pic_url\\\":\\\"(.*?)\\\",\\\"username\\\":\\\"(.*?)\\\",\\\"followed_by_viewer\\\":(true|false),\\\"has_public_story\\\":(true|false),\\\"is_private\\\":(true|false),\\\"is_unpublished\\\":(true|false),\\\"is_verified\\\":(true|false),\\\"edge_followed_by\\\":{\\\"count\\\":([0-9]+)},\\\"edge_owner_to_timeline_media\\\":{\\\"count\\\":([0-9]+)@';
preg_match($regex, $output, $result);

if (isset($result[2])) {
    $photo = str_replace('\\\\\\', '', $result[2]);
}
if (isset($result[9])) {
    $followers = $result[9];
}
if (isset($result[10])) {
    $postCount = $result[10];
}

preg_match_all('@\\\"thumbnail_src\\\":\\\"(.*?)\\\"@', $output, $result);
$posts = array_map(function ($image) {
    return str_replace('\\\\\\', '', $image);
}, array_slice($result[1], 0, 5));

if (!file_exists(__DIR__ . '/' . $username . '.jpg') && $photo) {
    file_put_contents(__DIR__ . '/' . $username . '.jpg', file_get_contents($photo));
}

echo json_encode([
    'username' => $username,
    'photo' => $photo,
    'followers' => $followers,
    'postCount' => $postCount,
    'posts' => $posts
]);
exit

?>

<img src="<?= $username ?>.jpg" alt="">
<h3><?= $username ?></h3>
<p>
    Takipci: <?= $followers ?> - Post: <?= $postCount ?>
</p>

<div>
    <?php foreach ($posts as $post): ?>
        <img src="<?= $post ?>" alt="">
    <?php endforeach; ?>
</div>
