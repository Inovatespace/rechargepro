package com.example.enugubackground;




import java.io.File;
import java.io.IOException;
import java.net.URLEncoder;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.mime.content.FileBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.util.EntityUtils;

import com.example.enugubackground.AndroidMultiPartEntity.ProgressListener;

import android.app.Service;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.os.AsyncTask;
import android.os.Environment;
import android.os.Handler;
import android.os.IBinder;
import android.os.SystemClock;
import android.widget.Toast;

public class MyService extends Service {
	public MyService() {
	}

	@Override
	public IBinder onBind(Intent intent) {
		//throw new UnsupportedOperationException("Not yet implemented");
		 return null;
	}
	
	
	public Intent intent;
	public static final String BROADCAST_ACTION = "com.javacodegeeks.android.androidtimerexample.MainActivity";
	private Handler handler = new Handler();
    public long initial_time;
    long timeInMilliseconds = 0L;
    public String link = "";
 
    MySQLiteHelper db;
    SQLiteDatabase database;
    
	@Override
	public void onCreate() {
		Toast.makeText(this, "Service was Created", Toast.LENGTH_LONG).show();
        super.onCreate();
        
        initial_time = SystemClock.uptimeMillis();
        intent = new Intent(BROADCAST_ACTION);  
        handler.removeCallbacks(sendUpdatesToUI);
        handler.postDelayed(sendUpdatesToUI, 1000); // 1 second
        db = new MySQLiteHelper(this);
       // open();
	}
	
	 protected void onResume() {
	    //open();
	  }

	  protected void onPause() {
	   // close();
	  }
	
	public void open() throws SQLException {
	    database = db.getWritableDatabase();
	  }
	
	  public void close() {
		    db.close();
		  }
	
	public void deleteenforcement(String Id) {
		open();
	    //database.delete("enforcement", "id = " + Id, null);
		database.execSQL("DELETE FROM enforcement WHERE id <= '"+Id+"';");
		close();
	  }
	
	public String getvnumber(String Id){
		open();
		String selectQuery = "SELECT tcarnumber FROM enforcement WHERE id = '"+Id+"' LIMIT 1";
		Cursor cursor = database.rawQuery(selectQuery, null);
	     cursor.moveToFirst();
	     String adr = "";
	     while(cursor.isAfterLast() == false){
	     	adr = 	cursor.getString(cursor.getColumnIndex("tcarnumber"));
	     cursor.moveToNext();
	     }
	     close();
	    return adr;
	}
	
	public String getallenforcement(){
		open();
		String selectQuery = "SELECT * FROM enforcement ORDER BY id ASC LIMIT 1";
		Cursor cursor = database.rawQuery(selectQuery, null);
	     cursor.moveToFirst();
	     String adr = "";
	     while(cursor.isAfterLast() == false){   	
	     	adr += 	cursor.getString(cursor.getColumnIndex("id"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("username"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("tlocation"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("toffence"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("tcarnumber"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("tEcordinate"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("tNcordinate"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("tpcn"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("tdate"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("ctype"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("image1"))+"@";
	     	adr += 	cursor.getString(cursor.getColumnIndex("image2"));
	     cursor.moveToNext();
	     }
	     close();
	    return adr;
	}
	
	
	public void dellid(String navnumber, String naid){
		deleteenforcement(naid);
		
		File sourceFilea = new File(Environment.getExternalStorageDirectory().getAbsolutePath() + "/evidence/"+navnumber+"a.png");
		File sourceFileb = new File(Environment.getExternalStorageDirectory().getAbsolutePath() + "/evidence/"+navnumber+"b.png");
		if (sourceFilea.exists()) {sourceFilea.delete();}
		if (sourceFileb.exists()) {sourceFileb.delete();}
		
		
	}
	

	
	  private Runnable sendUpdatesToUI = new Runnable() {
	        public void run() {
	        	//String result = 
	        			uploadFile();
	          //Toast.makeText(getBaseContext(),result,Toast.LENGTH_SHORT).show();
	        	
	        	
	          
               // Toast.makeText(getApplicationContext(), "done",Toast.LENGTH_SHORT).show();          
	            handler.postDelayed(this, 100000); // 1 seconds 600000/10 minutes
	        }
	    }; 
	    
	   
	    

	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    final Context context = this;
		long totalSize = 0;
	    


			@SuppressWarnings("deprecation")
			private void uploadFile() {
				String responseString = null;

				worker appsetting = new worker();
				link = appsetting.website();
				
				 // DatabaseHandler db = new DatabaseHandler(context);
				  String worktosend = getallenforcement();//.getallenforcement(); 

			       
			        // make sure the fields are not empty
				        if (worktosend.length()>0)
				        {    	
				        	String[] responsearray = worktosend.split("\\@");
				        	
							HttpClient httpclient = new DefaultHttpClient();
							HttpPost httppost = new HttpPost(link+"/api/abuja/towtruck.php");
				try {
					
					AndroidMultiPartEntity entity = new AndroidMultiPartEntity(
							new ProgressListener() {
								@Override
								public void transferred(long num) {
									publishProgress((int) ((num / (float) totalSize) * 100));
								}
							});

					File sourceFilea = new File(Environment.getExternalStorageDirectory().getAbsolutePath()+"/evidence/"+responsearray[4]+"a.png");
					File sourceFileb = new File(Environment.getExternalStorageDirectory().getAbsolutePath()+"/evidence/"+responsearray[4]+"b.png");
	
					if(sourceFilea.exists()){
						entity.addPart("imagea", new FileBody(sourceFilea));	
					}
					
					if(sourceFileb.exists()){
						entity.addPart("imageb", new FileBody(sourceFileb));	
					}

	
					

					String query1 = URLEncoder.encode(responsearray[4], "utf-8");
					String query2 = URLEncoder.encode(responsearray[3], "utf-8");
					String query3 = URLEncoder.encode(responsearray[2], "utf-8");
					String query4 = URLEncoder.encode(responsearray[1], "utf-8");
					String query5 = URLEncoder.encode(responsearray[5], "utf-8");
					String query6 = URLEncoder.encode(responsearray[6], "utf-8");
					String query7 = URLEncoder.encode(responsearray[0], "utf-8");
					String query8 = URLEncoder.encode(responsearray[8], "utf-8");
					String query9 = URLEncoder.encode(responsearray[7], "utf-8");
					String query10 = URLEncoder.encode(responsearray[9], "utf-8");
					

					entity.addPart("vehiclenumber", new StringBody(query1));
					entity.addPart("offence",new StringBody(query2));
					entity.addPart("location", new StringBody(query3));
					entity.addPart("officer",new StringBody(query4));
					entity.addPart("lo", new StringBody(query5));
					entity.addPart("la", new StringBody(query6));
					entity.addPart("id", new StringBody(query7));
					entity.addPart("pcn", new StringBody(query9));
					entity.addPart("date", new StringBody(query8));
					entity.addPart("ctype", new StringBody(query10));

					totalSize = entity.getContentLength();
					httppost.setEntity(entity);

					// Making server call
					HttpResponse response = httpclient.execute(httppost);
					HttpEntity r_entity = response.getEntity();

					int statusCode = response.getStatusLine().getStatusCode();
					if (statusCode == 200) {
						// Server response
						responseString = EntityUtils.toString(r_entity);
					} else {
						responseString = "Error occurred! Http Status Code: "+ statusCode;
					}
					
					
					Toast.makeText(getApplicationContext(), responseString,Toast.LENGTH_SHORT).show();
					

					   String[] respons = responseString.split("@");
									       if(respons[0].equals("ok")){
									    	   String navnumber = getvnumber(respons[1]);//db.getvnumber(respons[1]);
									    	   if(navnumber.length() > 0){		
									    		 dellid(navnumber, respons[1]);	
									    	   }
									       }
									       
								
									       

				} catch (ClientProtocolException e) {
					responseString = e.toString();
				} catch (IOException e) {
					responseString = e.toString();
				}
				
				        }

				        Toast.makeText(getApplicationContext(), responseString,Toast.LENGTH_SHORT).show();

			}

		

		


	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    

	protected void publishProgress(int i) {
				// TODO Auto-generated method stub
				
			}

	@Override
	public void onStart(Intent intent, int startId) {
		// Perform your long running operations here.
		Toast.makeText(this, "Service Started", Toast.LENGTH_LONG).show();

	}

	@Override
	public void onDestroy() {
		//Toast.makeText(this, "Service Destroyed", Toast.LENGTH_LONG).show();
	    super.onDestroy();
        handler.removeCallbacks(sendUpdatesToUI); 

	}


}