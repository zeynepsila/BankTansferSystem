<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Transfer;

class TransferController
{
    public function createTransfer(Request $request, Response $response)
{
    $data = json_decode($request->getBody()->getContents(), true);

    if (!isset($data['sender_id'], $data['receiver_id'], $data['amount'])) {
        $response->getBody()->write(json_encode(['error' => 'Missing required fields']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $transfer = new Transfer();
    $result = $transfer->initiateTransfer($data['sender_id'], $data['receiver_id'], $data['amount']);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
}


public function approveTransfer(Request $request, Response $response)
{
    $data = json_decode($request->getBody()->getContents(), true);

    if (!isset($data['transfer_id'], $data['approver_id'])) {
        $response->getBody()->write(json_encode(['error' => 'Missing required fields']));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $transfer = new Transfer();
    $result = $transfer->approveTransfer($data['transfer_id'], $data['approver_id']);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
}

}
