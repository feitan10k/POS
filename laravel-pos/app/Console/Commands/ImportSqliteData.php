<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
class ImportSqliteData extends Command
{
    protected $signature = 'db:import-sqlite';
    protected $description = 'Copy existing Pent.T POS data from SQLite into the configured MySQL database';
    public function handle(): int
    {
        $path=database_path('database.sqlite');
        if(!is_file($path)){ $this->error('ไม่พบฐานข้อมูล SQLite ต้นทาง'); return self::FAILURE; }
        $source=new PDO('sqlite:'.$path);$source->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $db=DB::connection();$db->beginTransaction();
        try {
            $users=[];
            foreach($source->query('SELECT * FROM users')->fetchAll(PDO::FETCH_ASSOC) as $row){
                $values=['name'=>$row['name'],'email'=>$row['email'],'password'=>$row['password'],'role'=>$row['role']??'cashier','updated_at'=>$row['updated_at']??now()];
                if(isset($row['email_verified_at']))$values['email_verified_at']=$row['email_verified_at'];
                $existing=$db->table('users')->where('email',$row['email'])->first();
                if($existing){$db->table('users')->where('id',$existing->id)->update($values);$users[$row['id']]=$existing->id;}
                else{$values['created_at']=$row['created_at']??now();$users[$row['id']]=$db->table('users')->insertGetId($values);}
            }
            $products=[];
            foreach($source->query('SELECT * FROM products')->fetchAll(PDO::FETCH_ASSOC) as $row){
                $values=['name'=>$row['name'],'category'=>$row['category'],'price'=>$row['price'],'stock'=>$row['stock']??0,'active'=>$row['active']??1,'detail'=>$row['detail']??null,'image'=>$row['image']??null,'updated_at'=>$row['updated_at']??now()];
                $existing=$db->table('products')->where('name',$row['name'])->first();
                if($existing){$db->table('products')->where('id',$existing->id)->update($values);$products[$row['id']]=$existing->id;}
                else{$values['created_at']=$row['created_at']??now();$products[$row['id']]=$db->table('products')->insertGetId($values);}
            }
            $customers=[];
            foreach($source->query('SELECT * FROM customers')->fetchAll(PDO::FETCH_ASSOC) as $row){
                $values=['name'=>$row['name'],'phone'=>$row['phone']??null,'points'=>$row['points']??0,'updated_at'=>$row['updated_at']??now()];
                $query=$db->table('customers')->where('name',$row['name']);
                if(!empty($row['phone']))$query->where('phone',$row['phone']);else $query->whereNull('phone');
                $existing=$query->first();
                if($existing){$db->table('customers')->where('id',$existing->id)->update($values);$customers[$row['id']]=$existing->id;}
                else{$values['created_at']=$row['created_at']??now();$customers[$row['id']]=$db->table('customers')->insertGetId($values);}
            }
            $sales=[];
            foreach($source->query('SELECT * FROM sales')->fetchAll(PDO::FETCH_ASSOC) as $row){
                $values=['receipt_no'=>$row['receipt_no'],'user_id'=>$users[$row['user_id']]??($db->table('users')->where('role','admin')->value('id')??1),'customer_id'=>isset($row['customer_id'])?($customers[$row['customer_id']]??null):null,'customer_name'=>$row['customer_name'],'subtotal'=>$row['subtotal']??$row['total'],'discount'=>$row['discount']??0,'total'=>$row['total'],'payment_method'=>$row['payment_method']??'cash','paid_amount'=>$row['paid_amount']??$row['total'],'change_amount'=>$row['change_amount']??0,'updated_at'=>$row['updated_at']??now()];
                $existing=$db->table('sales')->where('receipt_no',$row['receipt_no'])->first();
                if($existing){$db->table('sales')->where('id',$existing->id)->update($values);$sales[$row['id']]=$existing->id;}
                else{$values['created_at']=$row['created_at']??now();$sales[$row['id']]=$db->table('sales')->insertGetId($values);}
            }
            foreach($source->query('SELECT * FROM sale_items')->fetchAll(PDO::FETCH_ASSOC) as $row){
                $saleId=$sales[$row['sale_id']]??null;if(!$saleId)continue;$productId=isset($row['product_id'])?($products[$row['product_id']]??null):null;
                $values=['sale_id'=>$saleId,'product_id'=>$productId,'product_name'=>$row['product_name'],'price'=>$row['price'],'qty'=>$row['qty'],'subtotal'=>$row['subtotal']??($row['price']*$row['qty']),'updated_at'=>$row['updated_at']??now()];
                $existing=$db->table('sale_items')->where('sale_id',$saleId)->where('product_name',$row['product_name'])->first();
                if($existing)$db->table('sale_items')->where('id',$existing->id)->update($values);else{$values['created_at']=$row['created_at']??now();$db->table('sale_items')->insert($values);}
            }
                        $config=config('database.connections.mysql');
            try {
                $legacy=new PDO('mysql:host='.$config['host'].';port='.$config['port'].';dbname=cafe_db;charset=utf8mb4',$config['username'],$config['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
                $legacyProducts=[];
                foreach($legacy->query('SELECT id,name FROM products')->fetchAll(PDO::FETCH_ASSOC) as $product){$newId=$db->table('products')->where('name',$product['name'])->value('id');if($newId)$legacyProducts[$product['id']]=$newId;}
                $adminId=$db->table('users')->where('role','admin')->value('id')??$db->table('users')->value('id');
                foreach($legacy->query('SELECT * FROM orders ORDER BY id')->fetchAll(PDO::FETCH_ASSOC) as $order){
                    $receipt='LEGACY-'.$order['id'];$sale=$db->table('sales')->where('receipt_no',$receipt)->first();
                    if($sale){$saleId=$sale->id;}else{$saleId=$db->table('sales')->insertGetId(['receipt_no'=>$receipt,'user_id'=>$adminId,'customer_id'=>null,'customer_name'=>$order['customer_name'],'subtotal'=>$order['total'],'discount'=>0,'total'=>$order['total'],'payment_method'=>'cash','paid_amount'=>$order['total'],'change_amount'=>0,'created_at'=>$order['created_at']??now(),'updated_at'=>$order['created_at']??now()]);}
                    $itemQuery=$legacy->prepare('SELECT * FROM order_items WHERE order_id = ?');$itemQuery->execute([$order['id']]);
                    foreach($itemQuery->fetchAll(PDO::FETCH_ASSOC) as $item){$existingItem=$db->table('sale_items')->where('sale_id',$saleId)->where('product_name',$item['product_name'])->first();$values=['sale_id'=>$saleId,'product_id'=>$legacyProducts[$item['product_id']]??null,'product_name'=>$item['product_name'],'price'=>$item['price'],'qty'=>$item['qty'],'subtotal'=>$item['price']*$item['qty'],'updated_at'=>$order['created_at']??now()];if($existingItem)$db->table('sale_items')->where('id',$existingItem->id)->update($values);else{$values['created_at']=$order['created_at']??now();$db->table('sale_items')->insert($values);}}
                }
            } catch(\PDOException $legacyError) {
                $this->warn('ไม่พบฐานข้อมูล cafe_db เดิม หรือเชื่อมต่อไม่ได้ จึงข้ามการนำเข้าประวัติ PHP เดิม');
            }
            $db->commit();$this->info('นำเข้าข้อมูล SQLite และประวัติคำสั่งซื้อเดิมจาก cafe_db แล้ว');return self::SUCCESS;
        } catch(\Throwable $e){$db->rollBack();$this->error($e->getMessage());return self::FAILURE;}
    }
}

