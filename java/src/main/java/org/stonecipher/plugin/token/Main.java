package org.stonecipher.plugin.token;

import java.io.File;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Random;

import org.bukkit.plugin.java.JavaPlugin;
import org.bukkit.command.Command;
import org.bukkit.command.CommandSender;
import org.bukkit.configuration.file.FileConfiguration;
import org.bukkit.entity.Player;
import org.bukkit.event.Listener;

public class Main extends JavaPlugin implements Listener{
	
	Random rand = new Random();

	FileConfiguration config = getConfig();
	
	// Database configuration
	final private String username = config.getString("username");
	final private String password = config.getString("password");
	final private String url = "jdbc:mysql://" + config.getString("host") + "/" + config.getString("database");
	static private Connection conn;
	
	// Upon enable
	@Override
	public void onEnable() {
		
		// Only attempts to connect to the database if 
		setupConfig();
		if (!username.equals("username")){
			connectToDatabase();
			removeOutdatedTokens();
		}
		
	}
	
	// Upon disable
	@Override
	public void onDisable() {
		
		if (!username.equals("username")) {
			removeOutdatedTokens();
			disconnectFromDatabase();
		}
		
	}

	// Command execution
	@Override
	public boolean onCommand(CommandSender sender, Command cmd, String label, String[] args) {
		if (sender instanceof Player){
			Player player = (Player) sender; // Create new player object
			String uuid = player.getUniqueId().toString(); // Fetch UUID
			String token = setToken(uuid);
			if (token!="fail"){ // Set token in the database, returns true upon success	
				sender.sendMessage("§8[§7Auth§8] §rYour token is §e" + token);
				sender.sendMessage("§8[§7Auth§8] §rVisit https://cas.openredstone.org/register.php?token=" + token + " to register.");
			} else { // Upon token generation failure
				sender.sendMessage("§8[§7Auth§8] §rAn error occured, please contact your system administrator.");
			}
			return true;
		} else {
			sender.sendMessage("§8[§7Auth§8] §rThis command can only be ran in-game");
		} 
		return true;
	}
	
	// Sets up the config properly
	private void setupConfig(){
		try {
			if (!getDataFolder().exists()) {
				getDataFolder().mkdirs();
			}
			File file = new File(getDataFolder(), "config.yml");
			
			if (!file.exists()) {
				getLogger().info("Config.yml not found, creating!");
				getLogger().info("You need to stop the server to edit the config then restart!");
				saveDefaultConfig();
			} else {
				config.addDefault("database", "database");
				config.addDefault("username", "username");
				config.addDefault("password", "password");
				config.addDefault("host", "localhost:3306");
				config.addDefault("timeout", "1800");
				config.addDefault("length", "8");
				config.options().copyDefaults(true);
				saveConfig();
				getLogger().info("Config.yml found, loading!");
				getLogger().info("Database: " + config.getString("database"));
				getLogger().info("Username: " + config.getString("username"));
				getLogger().info("Token Timeout: " + config.getString("timeout"));
				getLogger().info("Token Length: " + config.getString("length"));
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	// Safely connects to the database and sets up the table if it isn't set
	private void connectToDatabase() {
		try { 
			conn = DriverManager.getConnection(url, username, password);
			String createTable = "CREATE TABLE IF NOT EXISTS auth_registrationtokens (token VARCHAR(16), m_uuid VARCHAR(32), time INT, UNIQUE KEY(uuid));";
			try {
				PreparedStatement table = conn.prepareStatement(createTable);
				table.executeUpdate();
			} catch (Exception e) {
				e.printStackTrace();
			}
		} catch (SQLException e) {
			e.printStackTrace();
		}
	}
	
	// Safely disconnects from the database
	private void disconnectFromDatabase(){
		try {
			if(conn!=null && !conn.isClosed()){ 
				conn.close();
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	// Removes tokens older than 30 minutes
	private void removeOutdatedTokens() {
		try {
			String removeOldTokens = "DELETE FROM auth_registrationtokens WHERE time < (UNIX_TIMESTAMP()-?);";
			PreparedStatement removeOldTokensPrepared = conn.prepareStatement(removeOldTokens);
			removeOldTokensPrepared.setString(1, config.getString("timeout"));
			removeOldTokensPrepared.executeUpdate();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	//REPLACE INTO the database so UUIDs don't get repeated
	private boolean setUserToken(String uuid, String token) {
		try {
			String insertToken = "REPLACE INTO auth_registrationtokens(token, m_uuid, time) VALUES (?, ?, UNIX_TIMESTAMP());";
			PreparedStatement insertTokenPrepared = conn.prepareStatement(insertToken);
			insertTokenPrepared.setString(1, token);
			insertTokenPrepared.setString(2, uuid);
			insertTokenPrepared.executeUpdate();
			return true;
		} catch (Exception e) {
			e.printStackTrace();
			return false;
		}
	}
	
	// Sets the token
	private String setToken(String uuid) {
		uuid = uuid.replace("-", ""); // Removes the dashed from the UUID for VARCHAR(32)
		removeOutdatedTokens();
		String token = generateUniqueToken();
		if(setUserToken(uuid, token)){
			return token;
		} else {
			return "fail";
		}
		
	}
	
	// Checks if the token exists or not
	private boolean tokenExists(String token) {
		try{	
			String tokenCheck = "SELECT EXISTS(SELECT * FROM auth_registrationtokens WHERE token=?);";
			PreparedStatement tokenCheckPrepared = conn.prepareStatement(tokenCheck);
			tokenCheckPrepared.setString(1,token);
			ResultSet result = tokenCheckPrepared.executeQuery();
			result.first();
			return result.getBoolean(1);
		} catch (Exception e) {
			e.printStackTrace();
		}
		return false;
	}
	
	// Continually generates a token until the token is unique
	private String generateUniqueToken() {
		while(true) {
			String token = generateToken();
			if(!tokenExists(token)) {
				return token;
			}
		}
	}
	
	// Simple utility to randomly create a token
	private String generateToken() { 
		// Simple random token generator. Was relatively cheap compared to the apache.utils.random
		String chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		int length = Integer.valueOf(config.getString("length"));
		StringBuilder random = new StringBuilder();
		for (int i = 0; i < length; i++) {
			random.append(chars.charAt(rand.nextInt(52)));
		}
		return random.toString();
	}
}
