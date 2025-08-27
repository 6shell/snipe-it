<?php

namespace App\Http\Controllers;

use App\Actions\Suppliers\DestroySupplierAction;
use App\Exceptions\ItemStillHasMaintenances;
use App\Exceptions\ItemStillHasAssets;
use App\Exceptions\ItemStillHasLicenses;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BulkSuppliersController extends Controller
{
    public function destroy(Request $request)
    {
        $this->authorize('delete', Supplier::class);

        $errors = [];
        foreach ($request->ids as $id) {
            $supplier = Supplier::find($id);
            if (is_null($supplier)) {
                $errors[] = trans('admin/suppliers/message.delete.not_found');
                continue;
            }
            try {
                DestroySupplierAction::run(supplier: $supplier);
            } catch (ItemStillHasAssets $e) {
                $errors[] = trans('general.bulk_delete_associations.assoc_assets', ['asset_count' => (int) $supplier->assets_count, 'item' => trans('general.supplier'), 'item_name' => $supplier->name]);
            } catch (ItemStillHasMaintenances $e) {
                $errors[] = trans('general.bulk_delete_associations.assoc_maintenances', ['asset_maintenances_count' => $supplier->asset_maintenances_count, 'item' => trans('general.supplier'), 'item_name' => $supplier->name]);
            } catch (ItemStillHasLicenses $e) {
                $errors[] = trans('general.bulk_delete_associations.assoc_licenses', ['licenses_count' => (int) $supplier->licenses_count, 'item' => trans('general.supplier'), 'item_name' => $supplier->name]);
            } catch (\Exception $e) {
                report($e);
                $errors[] = trans('general.something_went_wrong');
            }
        }
        if (count($errors) > 0) {
            return redirect()->route('suppliers.index')->with('multi_error_messages', $errors);
        } else {
            return redirect()->route('suppliers.index')->with('success', trans('admin/suppliers/message.delete.bulk_success'));
        }
    }
}
