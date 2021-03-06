<?php
App::uses('AppController', 'Controller');
/**
 * ItemPlaces Controller
 *
 * @property ItemPlace $ItemPlace
*/
class ItemPlacesController extends AppController {

	/**
	 * index method
	 *
	 * @return void
	 */
	public function index() {
		$this->ItemPlace->recursive = 0;
		$this->set('itemPlaces', $this->paginate());
	}

	/**
	 * view method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function view($id = null) {
		$this->ItemPlace->id = $id;
		if (!$this->ItemPlace->exists()) {
			throw new NotFoundException(__('Invalid item place'));
		}
		$this->set('itemPlace', $this->ItemPlace->read(null, $id));
	}
	
	public function select(){
		$data=$this->Session->read('data');
		if ($this->request->is('post')){
			$data['itemplace_id']=$this->request->data['ItemPlace']['place_id'];
			$this->Session->write('data',$data);
			$this->redirect($this->Session->read('return'));			
		}
		$item_places=$this->ItemPlace->find('all',array('fields'=>array('ItemPlace.id','Place.description'),'conditions'=>array('item_id'=>$data['included_item_id'],'count>='.$data['amount'])));
		$places=array();
		if (count($item_places)==0){
			$this->Session->setFlash(__('No location holds as many items, as you want to build in! Select less items to build in!'));
			$this->Session->delete('return');
			$this->Session->delete('data');
			$this->redirect(array('controller'=>'items','action'=>'view',$data['included_item_id']));
		}
		foreach ($item_places as $place){
			$places[$place['ItemPlace']['id']]=$place['Place']['description'];
		}
		$this->set(compact('places','return'));
	}

	/**
	 * add method
	 *
	 * @return void
	 */
	public function add() {
		if ($this->ItemPlace->Place->find('count')==0){
			$this->Session->setFlash(__('Please create a place first!'));
			$this->redirect(array('controller'=>'places'));
		} else	if ($this->request->is('post')) {
			$data=$this->request->data['ItemPlace'];
			$this->ItemPlace->recursive = 0;
			$first=$this->ItemPlace->find('first',array('conditions'=>array('ItemPlace.item_id'=>$data['item_id'],'ItemPlace.place_id'=>$data['place_id'])));
			if (empty($first)){
				$this->ItemPlace->create();
				$saved=$this->ItemPlace->save($this->request->data);
			} else {
				$first=$first['ItemPlace'];
				$first['count']+=$this->request->data['ItemPlace']['count'];
				$saved=$this->ItemPlace->save($first);
			}
			//*/
			
			
			if ($saved) {
				$this->Session->setFlash(__('The item place has been saved'));
				$this->ItemPlace->read();
				$this->requestAction(
						array('controller'=>'events','action'=>'add'),
						array('Event'=>array('item_id'=>$this->ItemPlace->data['Item']['id'], 'description'=>(__('Moved to').' '.$this->ItemPlace->data['Place']['description']), 'user_id'=>$this->Auth->user('id'))));

				$this->redirect(array('controller'=>'items','action' => 'view',$this->ItemPlace->data['Item']['id']));
			} else {
				$this->Session->setFlash(__('The item place could not be saved. Please, try again.'));
			}
		}
		$places = $this->ItemPlace->Place->find('list');
		$dummy = $this->ItemPlace->Item->find('list');
		end($dummy);
		$key=key($dummy);
		$items=array($key=>$dummy[$key]);
		$this->set(compact('places', 'items'));
	}

	/**
	 * move method
	 *
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function move($id = null) {
		$this->ItemPlace->id = $id;
		if (!$this->ItemPlace->exists()) {
			throw new NotFoundException(__('Invalid item place'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->ItemPlace->read();

			$itemid=$this->request->data['ItemPlace']['item_id'];
			$movecount=$this->request->data['ItemPlace']['count'];
			$destination=$this->request->data['ItemPlace']['place_id'];
			$this->request->data['ItemPlace']['count']=$this->ItemPlace->data['ItemPlace']['count']-$movecount;
			$this->request->data['ItemPlace']['place_id']=$this->ItemPlace->data['ItemPlace']['place_id'];
			$this->request->data['ItemPlace']['id']=$id;

			if ($this->request->data['ItemPlace']['count']<0){ // more items moved than available
				$this->Session->setFlash(__('You try to move more objects than available'));
				$this->redirect($this->referer());
			}
				
			if ($this->ItemPlace->save($this->request->data)){
					
				$destinationEntry=$this->ItemPlace->findByPlaceIdAndItemId($destination,$itemid);
				if (empty($destinationEntry)){
					// no entry for destination, yet
					$destinationEntry=array('ItemPlace'=>array(
							'item_id'=>$itemid,
							'place_id'=>$destination,
							'count'=>$movecount));
					$this->ItemPlace->create();
				} else {
					// already some pieces of the item at the destination
					$destinationEntry['ItemPlace']['count']+=$movecount;
					unset($destinationEntry['Place']);
					unset($destinationEntry['Item']);
					$this->ItemPlace->read(null,$destinationEntry['ItemPlace']['id']);
				}
				if ($this->ItemPlace->save($destinationEntry)){
					$this->Session->setFlash(__('Moved '.$movecount.' Objects.'));
					$this->requestAction(
							array('controller'=>'events','action'=>'add'),
							array('Event'=>array('item_id'=>$itemid, 'description'=>(__('Moved '.$movecount.' items')), 'user_id'=>$this->Auth->user('id'))));						
					$this->redirect(array('controller'=>'items','action'=>'view',$itemid));
				}
			}
				

		} else {
			$this->request->data = $this->ItemPlace->read(null, $id);
		}

		$places = $this->ItemPlace->Place->find('list');
		if (isset($id)){
			$item=$this->ItemPlace->data['Item'];
			$items=array($item['id']=>$item['name']);
		} else $items = $this->ItemPlace->Item->find('list');
		$this->set(compact('places', 'items'));
	}

	/**
	 * delete method
	 *
	 * @throws MethodNotAllowedException
	 * @throws NotFoundException
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ItemPlace->id = $id;
		if (!$this->ItemPlace->exists()) {
			throw new NotFoundException(__('Invalid item place'));
		}
		$this->ItemPlace->read();
		if ($this->ItemPlace->delete()) {
			$this->Session->setFlash(__('Item place deleted'));
		} else $this->Session->setFlash(__('Item place was not deleted'));
		$this->redirect(array('controller'=>'items','action' => 'view',$this->ItemPlace->data['ItemPlace']['item_id']));
	}
}
