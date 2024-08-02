<?php
/*
Plugin Name: VK Dummy Data
Description: カスタム投稿タイプとタクソノミーを登録し、ダミーデータの作成と削除を行います。
Version: 1.0
Author: Your Name
*/

// カスタム投稿タイプとタクソノミーを登録
// カスタム投稿タイプとタクソノミーを登録
function register_custom_post_types_and_taxonomies() {
    // カスタム投稿タイプ1を登録
    register_post_type('custom_post_type_1', [
        'labels' => [
            'name' => 'Custom Post Type 1',
            'singular_name' => 'Custom Post Type 1',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,  // これを追加
        'rewrite' => ['slug' => 'custom-post-type-1'],
    ]);

    // カスタム投稿タイプ2を登録
    register_post_type('custom_post_type_2', [
        'labels' => [
            'name' => 'Custom Post Type 2',
            'singular_name' => 'Custom Post Type 2',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,  // これを追加
        'rewrite' => ['slug' => 'custom-post-type-2'],
    ]);

    // カスタムタクソノミーを登録
    register_taxonomy("taxonomy_1_1", 'custom_post_type_1', [
        'labels' => [
            'name' => "Custom Post Type 1 Taxonomy 1",
            'singular_name' => "Custom Post Type 1 Taxonomy 1",
        ],
        'public' => true,
        'hierarchical' => true, // カテゴリタイプ
        'show_in_rest' => true, // これを追加
        'rewrite' => ['slug' => "taxonomy-1-1"],
    ]);

    register_taxonomy("taxonomy_1_2", 'custom_post_type_1', [
        'labels' => [
            'name' => "Custom Post Type 1 Taxonomy 2",
            'singular_name' => "Custom Post Type 1 Taxonomy 2",
        ],
        'public' => true,
        'hierarchical' => false, // タグタイプ
        'show_in_rest' => true,  // これを追加
        'rewrite' => ['slug' => "taxonomy-1-2"],
    ]);

    register_taxonomy("taxonomy_2_1", 'custom_post_type_2', [
        'labels' => [
            'name' => "Custom Post Type 2 Taxonomy 1",
            'singular_name' => "Custom Post Type 2 Taxonomy 1",
        ],
        'public' => true,
        'hierarchical' => true, // カテゴリタイプ
        'show_in_rest' => true, // これを追加
        'rewrite' => ['slug' => "taxonomy-2-1"],
    ]);

    register_taxonomy("taxonomy_2_2", 'custom_post_type_2', [
        'labels' => [
            'name' => "Custom Post Type 2 Taxonomy 2",
            'singular_name' => "Custom Post Type 2 Taxonomy 2",
        ],
        'public' => true,
        'hierarchical' => false, // タグタイプ
        'show_in_rest' => true,  // これを追加
        'rewrite' => ['slug' => "taxonomy-2-2"],
    ]);
}
add_action('init', 'register_custom_post_types_and_taxonomies');

// 管理メニューにカスタムページを追加
function custom_dummy_data_menu() {
    add_menu_page(
        'VK Dummy Data',    // ページタイトル
        'VK Dummy Data',    // メニュータイトル
        'manage_options',   // 権限
        'custom-dummy-data',// メニューのスラッグ
        'custom_dummy_data_page', // コールバック関数
        'dashicons-admin-tools',  // アイコン
        20                   // メニュー位置
    );
}
add_action('admin_menu', 'custom_dummy_data_menu');

// 管理画面の内容を表示
function custom_dummy_data_page() {
    ?>
    <div class="wrap">
        <h1>VK Dummy Data</h1>
        <form id="dummy-data-form" method="post">
            <input type="hidden" name="action" value="custom_dummy_data_action">
            <input type="hidden" name="custom_dummy_data_action" value="create">
            <label for="post_count">作成する記事数:</label>
            <input type="number" name="post_count" id="post_count" value="1000">
            <label for="batch_size">バッチサイズ:</label>
            <input type="number" name="batch_size" id="batch_size" value="500">
            <button type="submit" class="button button-primary">ダミーデータ作成</button>
        </form>
        <br>
        <form id="dummy-data-delete-form" method="post">
            <input type="hidden" name="action" value="custom_dummy_data_action">
            <input type="hidden" name="custom_dummy_data_action" value="delete">
            <button type="submit" class="button button-secondary">ダミーデータ削除</button>
        </form>
        <div id="progress" style="margin-top: 20px;"></div>
    </div>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#dummy-data-form').submit(function(e) {
            e.preventDefault();
            $('#progress').html('Processing...');
            var postCount = $('#post_count').val();
            var batchSize = $('#batch_size').val();
            processPosts(0, postCount, batchSize, 'custom_post_type_1', function() {
                processPosts(0, postCount, batchSize, 'custom_post_type_2');
            });
        });

        $('#dummy-data-delete-form').submit(function(e) {
            e.preventDefault();
            $('#progress').html('Processing...');
            $.post(ajaxurl, $(this).serialize(), function(response) {
                $('#progress').html(response);
            });
        });

        function processPosts(offset, total, batchSize, postType, callback) {
            $.post(ajaxurl, {
                action: 'custom_dummy_data_action',
                custom_dummy_data_action: 'create',
                post_count: total,
                batch_size: batchSize,
                offset: offset,
                post_type: postType
            }, function(response) {
                if (response.success) {
                    $('#progress').html(response.data.message);
                    if (response.data.offset < total) {
                        processPosts(response.data.offset, total, batchSize, postType, callback);
                    } else if (callback) {
                        callback();
                    } else {
                        $('#progress').html('All data has been inserted.');
                    }
                } else {
                    $('#progress').html('Error: ' + response.data.message);
                }
            }).fail(function(xhr, textStatus, errorThrown) {
                $('#progress').html('Request failed: ' + textStatus + ' - ' + errorThrown);
            });
        }
    });
    </script>
    <?php
}

// POSTリクエストを処理
function handle_custom_dummy_data_actions() {
    if (!isset($_POST['custom_dummy_data_action'])) {
        return;
    }

    if ($_POST['custom_dummy_data_action'] === 'create') {
        $post_count = isset($_POST['post_count']) ? intval($_POST['post_count']) : 1000;
        $batch_size = isset($_POST['batch_size']) ? intval($_POST['batch_size']) : 500;
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'custom_post_type_1';
        create_dummy_data($post_count, $batch_size, $offset, $post_type);
    } elseif ($_POST['custom_dummy_data_action'] === 'delete') {
        delete_dummy_data();
    }

    wp_die(); // この関数で終了しないと、0が返ってくる
}
add_action('wp_ajax_custom_dummy_data_action', 'handle_custom_dummy_data_actions');

// カスタムタクソノミーの作成とIDの取得
function create_custom_taxonomies() {
    global $wpdb;

    $taxonomy_ids = [
        'custom_post_type_1' => [
            'taxonomy_1_1' => [],
            'taxonomy_1_2' => [],
        ],
        'custom_post_type_2' => [
            'taxonomy_2_1' => [],
            'taxonomy_2_2' => [],
        ]
    ];

    for ($i = 1; $i <= 10; $i++) {
        // Custom Post Type 1
        $term = term_exists("Custom Post Type 1 Term 1 $i", "taxonomy_1_1");
        if ($term === 0 || $term === null) {
            $taxonomy = wp_insert_term("Custom Post Type 1 Term 1 $i", "taxonomy_1_1");
            if (!is_wp_error($taxonomy)) {
                $taxonomy_ids['custom_post_type_1']['taxonomy_1_1'][] = $taxonomy['term_id'];
            }
        } else {
            $taxonomy_ids['custom_post_type_1']['taxonomy_1_1'][] = $term['term_id'];
        }

        $term = term_exists("Custom Post Type 1 Term 2 $i", "taxonomy_1_2");
        if ($term === 0 || $term === null) {
            $taxonomy = wp_insert_term("Custom Post Type 1 Term 2 $i", "taxonomy_1_2");
            if (!is_wp_error($taxonomy)) {
                $taxonomy_ids['custom_post_type_1']['taxonomy_1_2'][] = $taxonomy['term_id'];
            }
        } else {
            $taxonomy_ids['custom_post_type_1']['taxonomy_1_2'][] = $term['term_id'];
        }

        // Custom Post Type 2
        $term = term_exists("Custom Post Type 2 Term 1 $i", "taxonomy_2_1");
        if ($term === 0 || $term === null) {
            $taxonomy = wp_insert_term("Custom Post Type 2 Term 1 $i", "taxonomy_2_1");
            if (!is_wp_error($taxonomy)) {
                $taxonomy_ids['custom_post_type_2']['taxonomy_2_1'][] = $taxonomy['term_id'];
            }
        } else {
            $taxonomy_ids['custom_post_type_2']['taxonomy_2_1'][] = $term['term_id'];
        }

        $term = term_exists("Custom Post Type 2 Term 2 $i", "taxonomy_2_2");
        if ($term === 0 || $term === null) {
            $taxonomy = wp_insert_term("Custom Post Type 2 Term 2 $i", "taxonomy_2_2");
            if (!is_wp_error($taxonomy)) {
                $taxonomy_ids['custom_post_type_2']['taxonomy_2_2'][] = $taxonomy['term_id'];
            }
        } else {
            $taxonomy_ids['custom_post_type_2']['taxonomy_2_2'][] = $term['term_id'];
        }
    }

    return $taxonomy_ids;
}

// ランダムな日本語の単語リスト
$words = [
    'こんにちは', '世界', 'プログラム', 'テスト', 'サンプル', 'データ', '情報', 'コンテンツ', 'ウェブ', 'サイト',
    'ユーザー', '管理', 'システム', '記事', '投稿', 'コメント', '画像', '写真', '動画', 'リンク',
    'ページ', 'カテゴリ', 'タグ', 'テーマ', 'プラグイン', '設定', 'オプション', 'メニュー', 'ヘッダー', 'フッター'
];

// ランダムな文章を生成する関数
function generate_random_content($word_list, $min_words = 50, $max_words = 100) {
    $word_count = rand($min_words, $max_words);
    $content = '';
    for ($i = 0; $i < $word_count; $i++) {
        $content .= $word_list[array_rand($word_list)] . ' ';
    }
    return trim($content);
}

function insert_dummy_posts($count, $batch_size, $word_list, $taxonomy_ids, $post_type, $offset) {
    global $wpdb;

    $current_time = time();
    $ten_years_ago = strtotime('-10 years');
    $interval = intval(($current_time - $ten_years_ago) / $count); // 各投稿の間隔を整数に変換

    $values = [];
    $meta_values = [];
    $term_relationships = [];

    // トランザクションの開始
    $wpdb->query('START TRANSACTION');

    for ($i = $offset; $i < $offset + $batch_size && $i < $count; $i++) {
        $post_title = ($post_type == 'custom_post_type_1' ? 'カスタム投稿タイプ1' : 'カスタム投稿タイプ2') . ' ' . $i;
        $post_content = generate_random_content($word_list);
        $post_name = 'dummy-' . $i;
        $post_time = date('Y-m-d H:i:s', $ten_years_ago + ($interval * $i));
        $post_time_gmt = get_gmt_from_date($post_time);
        $guid = home_url('/' . $post_name . '/');
        $values[] = $wpdb->prepare("(%s, %s, %s, %s, %s, %d, %s, %s, %s, %s, %s)",
            $post_title, $post_content, $post_time, $post_time_gmt, 'publish', 1, $post_time, $post_time_gmt, $post_type, $post_name, $guid);
    }

    if (!empty($values)) {
        $sql = "INSERT INTO {$wpdb->prefix}posts (post_title, post_content, post_date, post_date_gmt, post_status, post_author, post_modified, post_modified_gmt, post_type, post_name, guid)
                VALUES " . implode(", ", $values);

        $result = $wpdb->query($sql);

        if ($result === false) {
            $wpdb->query('ROLLBACK');
            wp_send_json_error(['message' => 'Database insert failed']);
        }

        // 最後に挿入された投稿のIDを取得
        $start_id = $wpdb->get_var("SELECT LAST_INSERT_ID()");
        $end_id = $start_id + count($values) - 1;

        // メタデータとタクソノミーの関係を挿入
        for ($id = $start_id; $id <= $end_id; $id++) {
            $meta_values[] = $wpdb->prepare("(%d, %s, %s)", $id, '_edit_last', 1);
            $meta_values[] = $wpdb->prepare("(%d, %s, %s)", $id, '_edit_lock', time() . ':1');

            // 各タクソノミーにランダムなタームを割り当てる
            foreach ($taxonomy_ids[$post_type] as $taxonomy => $terms) {
                if (!empty($terms)) {
                    $random_term_id = $terms[array_rand($terms)];
                    $term_taxonomy_id = $wpdb->get_var($wpdb->prepare(
                        "SELECT term_taxonomy_id FROM {$wpdb->prefix}term_taxonomy WHERE term_id = %d AND taxonomy = %s",
                        $random_term_id, $taxonomy
                    ));
                    if ($term_taxonomy_id) {
                        $term_relationships[] = $wpdb->prepare("(%d, %d)", $id, $term_taxonomy_id);
                    }
                }
            }
        }

        if (!empty($meta_values)) {
            $meta_sql = "INSERT INTO {$wpdb->prefix}postmeta (post_id, meta_key, meta_value) VALUES " . implode(", ", $meta_values);
            $wpdb->query($meta_sql);
        }

        if (!empty($term_relationships)) {
            $term_relationships_sql = "INSERT INTO {$wpdb->prefix}term_relationships (object_id, term_taxonomy_id) VALUES " . implode(", ", $term_relationships);
            $wpdb->query($term_relationships_sql);
        }
    }

    // トランザクションのコミット
    $wpdb->query('COMMIT');

    $progress = min(round(($offset + $batch_size) / $count * 100), 100);
    wp_send_json_success(['message' => "Inserted $progress% of $count posts for $post_type.", 'offset' => $offset + $batch_size]);

    flush();
    // パーマリンク設定をリフレッシュしてURLの問題を解消
    flush_rewrite_rules();
}

function create_dummy_data($post_count, $batch_size, $offset, $post_type) {
    // カスタムタクソノミーを作成
    $taxonomy_ids = create_custom_taxonomies();

    // 指定された数のダミー投稿を挿入
    insert_dummy_posts($post_count, $batch_size, $GLOBALS['words'], $taxonomy_ids, $post_type, $offset);
}

function delete_dummy_data() {
    global $wpdb;

    // カスタム投稿タイプ1と2の投稿を削除
    $post_types = ['custom_post_type_1', 'custom_post_type_2'];
    foreach ($post_types as $post_type) {
        $post_ids = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = %s", $post_type));
        if (!empty($post_ids)) {
            $ids_string = implode(',', array_map('intval', $post_ids));
            $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE ID IN ($ids_string)");
            $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE post_id IN ($ids_string)");
            $wpdb->query("DELETE FROM {$wpdb->prefix}term_relationships WHERE object_id IN ($ids_string)");
        }
    }

    // タクソノミーを削除
    for ($i = 1; $i <= 10; $i++) {
        wp_delete_term(get_term_by('name', "Term 1 $i", "taxonomy_1_1")->term_id, "taxonomy_1_1");
        wp_delete_term(get_term_by('name', "Term 2 $i", "taxonomy_1_2")->term_id, "taxonomy_1_2");
        wp_delete_term(get_term_by('name', "Term 1 $i", "taxonomy_2_1")->term_id, "taxonomy_2_1");
        wp_delete_term(get_term_by('name', "Term 2 $i", "taxonomy_2_2")->term_id, "taxonomy_2_2");
    }

    echo 'Deleted dummy data.<br>';
}
?>
