<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    // Display expenses.
    public function index()
    {
        try {
            $expenses = Expense::all();
            return response()->json($expenses, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching expenses: " . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch expenses'], 500);
        }
    }

    //Store a newly created expense.
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        try {
            $expense = Expense::create($validatedData);
            return response()->json(['message' => 'Expense created successfully', 'expense' => $expense], 201);
        } catch (\Exception $e) {
            Log::error("Error creating expense: " . $e->getMessage());
            return response()->json(['message' => 'Failed to create expense'], 500);
        }
    }

    //Display expense.
    public function show($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            return response()->json($expense, 200);
        } catch (\Exception $e) {
            Log::error("Error fetching expense with ID {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Expense not found'], 404);
        }
    }

    //Update expense.
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        try {
            $expense = Expense::findOrFail($id);
            $expense->update($validatedData);
            return response()->json(['message' => 'Expense updated successfully', 'expense' => $expense], 200);
        } catch (\Exception $e) {
            Log::error("Error updating expense with ID {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Failed to update expense'], 500);
        }
    }

    //Remove expense.
    public function destroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();
            return response()->json(['message' => 'Expense deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting expense with ID {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Failed to delete expense'], 500);
        }
    }
}
