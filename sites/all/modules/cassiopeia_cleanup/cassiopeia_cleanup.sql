-- Script SQL để làm trắng cơ sở dữ liệu
-- CẢNH BÁO: Chỉ sử dụng sau khi đã sao lưu cơ sở dữ liệu
-- Các lệnh này sẽ xóa vĩnh viễn dữ liệu

-- Xóa nội dung Cassiopeia
TRUNCATE TABLE cassiopeia_content;
TRUNCATE TABLE tbl_tags;

-- Xóa dữ liệu bài viết khách
TRUNCATE TABLE cassiopeia_guest_post_article;
TRUNCATE TABLE cassiopeia_guest_post_article_tag;

-- Xóa dữ liệu website
TRUNCATE TABLE cassiopeia_guest_post_website;
TRUNCATE TABLE cassiopeia_guest_post_website_category;

-- Xóa node và dữ liệu liên quan
-- TRUNCATE TABLE node;
-- TRUNCATE TABLE node_revision;
-- TRUNCATE TABLE field_data_body;
-- TRUNCATE TABLE field_revision_body;

-- Xóa bình luận
-- TRUNCATE TABLE comment;

-- Xóa taxonomy terms (không xóa vocabularies)
-- DELETE FROM taxonomy_term_data;
-- DELETE FROM taxonomy_term_hierarchy;

-- Xóa người dùng (không xóa user ID 1)
-- DELETE FROM users WHERE uid > 1;
-- DELETE FROM users_roles WHERE uid > 1;

-- Xóa cache
TRUNCATE TABLE cache;
TRUNCATE TABLE cache_block;
TRUNCATE TABLE cache_bootstrap;
TRUNCATE TABLE cache_field;
TRUNCATE TABLE cache_filter;
TRUNCATE TABLE cache_form;
TRUNCATE TABLE cache_image;
TRUNCATE TABLE cache_menu;
TRUNCATE TABLE cache_page;
TRUNCATE TABLE cache_path;

-- Xóa watchdog logs
TRUNCATE TABLE watchdog;
