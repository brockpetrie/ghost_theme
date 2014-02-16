<?php

	Class extension_ghost_theme extends Extension {

		private function getGravatarUrl($email, $size) {
			$emailHash = md5(strtolower(trim($email)));
			$urlPref = 'https://gravatar.com/avatar/';
			$urlSuff = '?s=' . $size . '&d=mm';
			return $urlPref . $emailHash . $urlSuff;
		}

		public function getSubscribedDelegates(){
			return array(
				array(
					'page'		=> '/backend/',
					'delegate'	=> 'AdminPagePreGenerate',
					'callback'	=> 'appendAssets'
				),
				array(
					'page'		=> '/system/authors/',
					'delegate'	=> 'AddElementstoAuthorForm',
					'callback'	=> 'appendGravatar'
				)
			);
		}


		// Delegate `AdminPagePreGenerate` calls this, which we use to append our fancy assets to all admin pages

		public function appendAssets($context) {
			if ($context['oPage'] instanceof AjaxPage) return false;
			$admin = Administration::instance()->Page;
			$admin->addElementToHead(new XMLElement('link', NULL, array('rel' => 'icon', 'type' => 'images/png', 'href' => URL . '/extensions/ghost_theme/assets/bookmark.png')), 41);
			$admin->addScriptToHead(URL . '/extensions/ghost_theme/assets/fastclick.js', 1001);
			$admin->addStylesheetToHead('//fonts.googleapis.com/css?family=Open+Sans:400,300,700', 'screen', 1002);
			$admin->addStylesheetToHead(URL . '/extensions/ghost_theme/assets/ghost_theme.css', 'screen', 1003);
			$admin->addScriptToHead(URL . '/extensions/ghost_theme/assets/ghost_theme.js', 1004);
			if ($context['oPage'] instanceof contentLogin) {
				$admin->Body->addClass('login');
				if ($admin->_context[0] == 'retrieve-password') { // Retrieve password form
					$admin->Body->addClass('forgotpass');
					$admin->Form->getChildByName('fieldset',0)->getChildByName('label',0)->getChildByName('input',0)->setAttributeArray(array('placeholder' => 'Email or username', 'class' => 'forgotpass'));
					$admin->Form->getChildByName('fieldset',0)->getChildByName('label',0)->setValue('');
				} else { // Regular login form
					$admin->Form->getChildByName('fieldset',0)->getChildByName('label',0)->getChildByName('input',0)->setAttribute('placeholder', 'Username');
					$admin->Form->getChildByName('fieldset',0)->getChildByName('label',0)->setValue('');
					$admin->Form->getChildByName('fieldset',0)->getChildByName('label',1)->getChildByName('input',0)->setAttribute('placeholder', 'Password');
					$admin->Form->getChildByName('fieldset',0)->getChildByName('label',1)->setValue('');
					$admin->Form->getChildByName('div',0)->getChildByName('button',0)->setValue('Log In');
				}
			}
		}

		public function fetchNavigation() {
			$author = Administration::instance()->Author;
			$gravatar = $this::getGravatarUrl($author->get('email'), 40);
			$children = array();
			$children[] = array('name' => 'Your Profile', 'relative' => false, 'link' => '/system/authors/edit/' . $author->get('id'));
			if ($author->isManager()) $children[] = array('name' => 'Authors', 'relative' => false, 'link' => '/system/authors/');
			//if ($author->isDeveloper()) $children[] = array('name' => 'Extensions', 'relative' => false, 'link' => '/system/extensions/');
			//if ($author->isDeveloper()) $children[] = array('name' => 'System Preferences', 'relative' => false, 'link' => '/system/preferences/');
			$children[] = array('name' => 'Sign Out', 'relative' => false, 'link' => '/logout/');
			
			return array(
				array(
					'name' 		=> '<span class="avatar"><img src="' . $gravatar . '" /></span> ' . $author->getFullName(),
					'type'		=> 'structure',
					'index'		=> '301',
					'children'	=> $children
				)
			);
		}

		public function appendGravatar($context) {
			$fieldset = $context['form']->getChildByName('fieldset',0);
			$label = new XMLElement('label', __('Profile Image <br />'), array('class' => 'gravatar'));
			$img = new XMLElement('img', NULL, array('src' => $this::getGravatarUrl($context['author']->get('email'), 120)));
			$a = new XMLElement('a', $img, array('href' => 'https://en.gravatar.com/connect/', 'target' => '_blank'));
			$p = new XMLElement('p', __('<i>Change your profile picture at <a href="https://en.gravatar.com/connect/" target="_blank">Gravatar.com</a>.</i>'), array('class' => 'help'));
			$label->appendChild($a);
			$label->appendChild($p);
			$fieldset->insertChildAt(1, $label);
		}

	}
