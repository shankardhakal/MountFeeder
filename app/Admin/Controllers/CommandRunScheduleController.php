<?php

namespace App\Admin\Controllers;

use App\Repository\CommandRunScheduleRepository;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Support\Facades\Request;
use Prettus\Validator\Exceptions\ValidatorException;

class CommandRunScheduleController extends AdminController
{
    protected CommandRunScheduleRepository $repository;

    /**
     * CommandRunScheduleController constructor.
     * @param  CommandRunScheduleRepository  $repository
     */
    public function __construct(CommandRunScheduleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  Request  $request
     * @return mixed|void
     *
     * @throws ValidatorException
     */
    public function store(Request $request)
    {
        $this->repository->create($request->all());

        return response()->json(['message'=>'Command scheduled successfully']);
    }
}
