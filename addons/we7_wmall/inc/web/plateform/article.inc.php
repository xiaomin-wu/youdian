<?php
/**
 * 外送系统
 * @author 灯火阑珊
 * @QQ 2471240272
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$_W['page']['title'] = '新闻-' . $_W['we7_wmall']['config']['title'];
$do = 'article';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'category_post';

if($op == 'category_post') {
	if(checksubmit('submit')) {
		$i = 0;
		if(!empty($_GPC['title'])) {
			foreach($_GPC['title'] as $k => $v) {
				$title = trim($v);
				if(empty($title)) {
					continue;
				}
				$data = array(
					'uniacid' => $_W['uniacid'],
					'title' => $title,
					'displayorder' => intval($_GPC['displayorder'][$k]),
					'status' => intval($_GPC['status'][$k]),
				);
				pdo_insert('tiny_wmall_article_category', $data);
				$i++;
			}
		}
		message('添加新闻分类成功', $this->createWebUrl('ptfarticle', array('op' => 'category')), 'success');
	}
	include $this->template('plateform/article-category');
}

if($op == 'category') {
	if($_W['isajax']) {
		$id = intval($_GPC['id']);
		$status = intval($_GPC['status']);
		pdo_update('tiny_wmall_article_category', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
		exit();
	}
	if(checksubmit('submit')) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k])
				);
				pdo_update('tiny_wmall_article_category', $data, array('id' => intval($v)));
			}
			message('修改新闻分类成功', referer(), 'success');
		}
	}
	$data = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_article_category') . ' ORDER BY displayorder DESC', array(':type' => 'news'));
	include $this->template('plateform/article-category');
}

if($op == 'category_del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_article_category', array('id' => $id));
	pdo_delete('tiny_wmall_article', array('cateid' => $id));
	message('删除分类成功', referer(), 'success');
}

if($op == 'post') {
	$_W['page']['title'] = '编辑新闻-新闻列表';
	$id = intval($_GPC['id']);
	$article = pdo_fetch('SELECT * FROM ' . tablename('tiny_wmall_article') . ' WHERE id = :id', array(':id' => $id));
	if(checksubmit()) {
		$title = trim($_GPC['title']) ? trim($_GPC['title']) : message('新闻标题不能为空', '', 'error');
		$cateid = intval($_GPC['cateid']) ? intval($_GPC['cateid']) : message('新闻分类不能为空', '', 'error');
		$content = trim($_GPC['content']) ? trim($_GPC['content']) : message('新闻内容不能为空', '', 'error');
		$data = array(
			'uniacid' => $_W['uniacid'],
			'title' => $title,
			'cateid' => $cateid,
			'thumb' => trim($_GPC['thumb']),
			'content' => htmlspecialchars_decode($content),
			'displayorder' => intval($_GPC['displayorder']),
			'click' => intval($_GPC['click']),
			'status' => intval($_GPC['status']),
			'createtime' => TIMESTAMP,
		);

		if(!empty($article['id'])) {
			pdo_update('tiny_wmall_article', $data, array('uniacid' => $_W['uniacid'], 'id' => $id));
		} else {
			pdo_insert('tiny_wmall_article', $data);
		}
		message('编辑新闻成功', $this->createWebUrl('ptfarticle', array('op' => 'list')), 'success');
	}
	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_article_category') . ' WHERE 1 ORDER BY displayorder DESC', array(':type' => 'news'));
	include $this->template('plateform/article');
}

if($op == 'list') {
	if($_W['isajax']) {
		$id = intval($_GPC['id']);
		$status = intval($_GPC['status']);
		pdo_update('tiny_wmall_article', array('status' => $status), array('uniacid' => $_W['uniacid'], 'id' => $id));
		exit();
	}
	$condition = ' WHERE 1';
	$cateid = intval($_GPC['cateid']);
	$createtime = intval($_GPC['createtime']);
	$title = trim($_GPC['title']);

	$params = array();
	if($cateid > 0) {
		$condition .= ' AND cateid = :cateid';
		$params[':cateid'] = $cateid;
	}
	if(!empty($title)) {
		$condition .= " AND title LIKE :title";
		$params[':title'] = "%{$title}%";
	}

	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	$sql = 'SELECT * FROM ' . tablename('tiny_wmall_article') . $condition . " ORDER BY displayorder DESC LIMIT " . ($pindex - 1) * $psize .',' .$psize;
	$articles = pdo_fetchall($sql, $params);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('tiny_wmall_article') . $condition, $params);
	$pager = pagination($total, $pindex, $psize);

	$categorys = pdo_fetchall('SELECT * FROM ' . tablename('tiny_wmall_article_category') . ' WHERE 1 ORDER BY displayorder DESC', array(':type' => 'news'), 'id');
	include $this->template('plateform/article');
}

if($op == 'batch_post') {
	if(checksubmit()) {
		if(!empty($_GPC['ids'])) {
			foreach($_GPC['ids'] as $k => $v) {
				$data = array(
					'title' => trim($_GPC['title'][$k]),
					'displayorder' => intval($_GPC['displayorder'][$k]),
					'click' => intval($_GPC['click'][$k]),
				);
				pdo_update('tiny_wmall_article', $data, array('uniacid' => $_W['uniacid'], 'id' => intval($v)));
			}
			message('编辑新闻列表成功', referer(), 'success');
		}
	}
}

if($op == 'del') {
	$id = intval($_GPC['id']);
	pdo_delete('tiny_wmall_article', array('id' => $id));
	message('删除新闻成功', referer(), 'success');
}



