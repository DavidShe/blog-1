<?php
$out = array();
require 'header.php';

if (isGET('post') && isValidEntry('posts', GET('post'))) {
  $postEntry = readEntry('posts', GET('post'));
  $out['title'] = $postEntry['title'];
  $out['titleHtml'] = '';
  $out['content'] .= '<div class="post">
  <h1 class="title">' . $out['title'] . managePost(GET('post')) . '</h1>
  <div class="date">' . toDate(GET('post')) . '</div>
  <div class="info">';
  foreach ($postEntry['tags'] as $tag) {
    $tagEntry = readEntry('tags', $tag);
    $tagName = $tagEntry['name'];
    $out['content'] .= '<a href="./view.php?tag=' . $tag . '">' . $tagName . '</a>';
  }
  $out['content'] .= '</div>
  <div class="content">' . $postEntry['content'] . '</div>
  </div>';
  $pages = pages($postEntry['comments']);
  $page = page($pages);
  if ($postEntry['comments']) {
    $commentCount = count($postEntry['comments']);
    $out['content'] .= '<div class="ccount">' . $commentCount . ($commentCount != 1 ? $lang['ncomments'] : $lang['ncomment']) . '</div>';
    $out['content'] .= '<div id="comments">';
    $first = true;
    foreach (pageItems($postEntry['comments'], $page) as $comment) {
      $out['content'] .= $first ? '' : '<div class="div">&middot; &middot; &middot; &middot; &middot;</div>';
      $first = false;
      $commentEntry = readEntry('comments', $comment);
      $out['content'] .= '<div id="' . $comment . '" class="comment">
      <div class="title">' . $commentEntry['commenter'] . manageComment($comment) . '</div>
      <div class="date">' . toDate($comment) . '</div>
      <div class="content">' . content($commentEntry['content']) . '</div>
      </div>';
    }
    $out['content'] .= '</div>';
  } else {
    $out['content'] .= '<div id="comments"></div>';
  }
  $out['content'] .= paging($page, $pages, './view.php?post=' . GET('post') . '#comments');
  if (!$postEntry['locked']) {
    $out['content'] .= '<form action="./add.php?comment=' . GET('post') . '" method="post">
    <p>' . text('name') . '</p>
    <p>' . textarea('content') . '</p>
    <p>' . submitSafe('send') . '</p>
    </form>';
  }
} else if (isGET('draft') && isValidEntry('drafts', GET('draft'))) {
  $draftEntry = readEntry('drafts', GET('draft'));
  $out['title'] = $draftEntry['title'];
  $out['titleHtml'] = '';
  $out['content'] .= '<div class="post">
  <h1 class="title">' . $out['title'] . manageDraft(GET('draft')) . '</h1>
  <div class="date">' . toDate(GET('draft')) . '</div>';
  $out['content'] .= '<div class="content">' . $draftEntry['content'] . '</div>
  </div>';
} else if (isGET('tag') && isValidEntry('tags', GET('tag'))) {
  $tagEntry = readEntry('tags', GET('tag'));
  $out['title'] = $tagEntry['name'];
  $out['titleHtml'] .= '<h1>' . $out['title'] . manageTag(GET('tag')) . '</h1>';
  $out['content'] .= '';
  if ($tagEntry['posts']) {
    foreach ($tagEntry['posts'] as $post) {
      $postEntry = readEntry('posts', $post);
      $title = $postEntry['title'];
      $out['content'] .= '<p><a href="./view.php?post=' . $post . '">' . $title . '</a>' . managePost($post) . ' &mdash; ' . toDate($post) . '</p>';
    }
  }
} else if (isGET('archive') && strlen(GET('archive')) === 7) {
  $archivedPosts = array();
  foreach (listEntry('posts') as $post)
    if (GET('archive') === substr($post, 0, 7))
      $archivedPosts[] = $post;
  if (!$archivedPosts) {
    redirect('index.php?404');
  } else {
    $out['title'] = date('M Y', strtotime(GET('archive')));
    $out['content'] .= '';
    foreach ($archivedPosts as $post) {
      $postEntry = readEntry('posts', $post);
      $title = $postEntry['title'];
      $out['content'] .= '<p><a href="./view.php?post=' . $post . '">' . $title . '</a>' . managePost($post) . ' &mdash; ' . toDate($post) . '</p>';
    }
  }
} else {
  redirect('index.php?404');
}

require 'templates/page.php';
?>
