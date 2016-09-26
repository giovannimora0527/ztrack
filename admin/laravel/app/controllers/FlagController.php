<?php

class FlagController extends \BaseController {


	 public function __construct()
    {
        

        $this->beforeFilter('serviceAuth', array('only' =>
                            array('postCreate', 'postUpdate', 'getDestroy')));
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getFlags()
	{		
		return Response::json(array('flags'=>Flag::all()));
	}

	public function getActives()
	{
		
		return Response::json(array('flags'=>Flag::getActives()));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function postCreate()
	{

		$file = Input::file('file');
		$input = Input::all();
		$input_array = (array)$input;
		$input_array['active']= ($input_array['active']=="true");
		$ext = $file->getClientOriginalExtension();
		//build main_image url

		$doc_root = $_SERVER["DOCUMENT_ROOT"];
		$num = getrandmax();
	
		$date = date("Y-m-d H:i:s");
		//new name for main_image
		$new_name = md5($num.$date);
		$input_array['main_image']= "photos/banners/".$new_name.".".$ext;
		//move to dir
		$file->move($doc_root."/photos/banners", $new_name.".".$ext);
		return Flag::buildFlag($input_array);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEdit($id)
	{
		
		return Response::json(array('flag'=> Flag::find($id)));

	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postUpdate()
	{
		$input = Input::all();
		$input_array = (array)$input;
		$input_array['active']=($input_array['active']=='true'||$input_array['active']==1);
		$doc_root = $_SERVER["DOCUMENT_ROOT"];
		if(Input::hasfile('file')){


		$file = Input::file('file');
		$band=unlink($doc_root."/".$input_array['main_image']);
		$vector= explode("/banners/",$input_array['main_image']);
		
		$file->move($doc_root."/photos/banners", $vector[1]);
		}
		
		return Flag::updateFlag($input_array);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDestroy($id)
	{
		return Flag::destroyFlag($id);
	}


}
