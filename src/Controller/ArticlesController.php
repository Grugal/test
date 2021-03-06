<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 */
class ArticlesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $articles = $this->paginate($this->Articles);

        $this->set('title', 'Requerimentos');
        $this->set(compact('articles'));
        $this->set('_serialize', ['articles']);
    }

    public function isAuthorized($user)
    {
        if ($this->request->action === 'add') {
            return true;
        }
        if (in_array($this->request->action, ['edit', 'delete'])) {
            $articleId = (int)$this->request->params['pass'] [0];
            if ($this->Articles->isOwnedBy($articleId, $user['id'])) {
                return true;
            }
        }
        return parent::isAuthorized($user);
    }

    /**
     * View method
     *
     * @param string|null $id Article id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $article = $this->Articles->get($id, [
            'contain' => []
        ]);

        $this->set('article', $article);
        $this->set('title', 'Requerimentos');
        $this->set('_serialize', ['article']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $article = $this->Articles->newEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->data);

            $article->user_id = $this->Auth->user('id');

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('SEU REQUERIMENTO FOI SALVO COM SUCESSO!'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('O REQUERIMENTO NÃO PÔDE SER SALVO, TENTE NOVAMENTE!'));
            }
        }

        $categories = $this->Articles->Categories->find('treeList');
        $this->set(compact('categories'));
        $this->set(compact('article'));

        $this->set('title', 'Requerimentos');
        $this->set('_serialize', ['article']);        
    }

    /**
     * Edit method
     *
     * @param string|null $id Article id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $article = $this->Articles->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $article = $this->Articles->patchEntity($article, $this->request->data);
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('O REQUERIMENTO FOI ENVIADO.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('O REQUERIMENTO NÃO PÔDE SER EDITADO, TENTE NOVAMENTE!'));
            }
        }
        $this->set(compact('article'));
        $this->set('_serialize', ['article']);
        $this->set('title', 'Requerimentos');
    }

    /**
     * Delete method
     *
     * @param string|null $id Article id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $article = $this->Articles->get($id);
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The article has been deleted.'));
        } else {
            $this->Flash->error(__('O REQUERIMENTO NÃO PÔDE SER DELETADO, TENTE NOVAMENTE!'));
        }

        return $this->redirect(['action' => 'index']);
        $this->set('title', 'Requerimentos');
    }
}
